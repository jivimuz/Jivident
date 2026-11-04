<?php

namespace Tests\Feature;

use App\Models\DaftarPasien;
use App\Models\Kamar;
use App\Models\Module;
use App\Models\Patient;
use App\Models\RuanganInap;
use App\Models\Serial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RawatInapControllerTest extends TestCase
{
    use RefreshDatabase;

    private function authorizedUser(): User
    {
        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addMonth()->toDateString(),
            'last_check' => now(),
        ]);

        $user = User::first();
        $user->access = (string) Module::where('code', 'RWI')->value('id');
        $user->save();

        return $user;
    }

    private function makeDaftarPasien(User $user, string $norm = 'RM-080'): DaftarPasien
    {
        $patient = Patient::create([
            'klienfk' => $user->klienfk, 'norm' => $norm, 'nik' => (string) random_int(1000000000, 9999999999), 'nama' => 'Pasien RWI Controller',
        ]);

        return DaftarPasien::create([
            'klienfk' => $user->klienfk,
            'noregistrasi' => 'REG-'.uniqid(),
            'ruanganfk' => 1,
            'pasienfk' => $patient->id,
            'tglregistrasi' => now(),
            'statuspasien' => 'LAMA',
            'status' => 'Di Periksa',
            'noantrian' => 'A1',
            'statuspembayaran' => 'Belum Verifikasi',
            'biaya_registrasi' => 20000,
        ]);
    }

    public function test_rawat_inap_page_requires_access(): void
    {
        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addMonth()->toDateString(),
            'last_check' => now(),
        ]);
        $user = User::first();
        $user->access = '999999';
        $user->save();

        $response = $this->actingAs($user)->get('/rawat-inap');

        $response->assertRedirect(route('error'));
    }

    public function test_can_admit_a_patient_to_an_available_room(): void
    {
        $user = $this->authorizedUser();
        $daftar = $this->makeDaftarPasien($user);
        $kamar = Kamar::create(['klienfk' => $user->klienfk, 'nama' => 'Kamar 1', 'tarif_per_hari' => 100000, 'status' => 'kosong']);

        $response = $this->actingAs($user)->post('/rawat-inap/admit', [
            'fk_daftarpasien' => $daftar->id,
            'fk_kamar' => $kamar->id,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('t_rawatinap', ['fk_daftarpasien' => $daftar->id, 'status' => 'dirawat']);
        $this->assertSame('terisi', $kamar->refresh()->status);
    }

    public function test_daftar_pasien_options_excludes_visits_with_an_active_admission(): void
    {
        $user = $this->authorizedUser();
        $daftarBelumAdmisi = $this->makeDaftarPasien($user, 'RM-081');
        $daftarSudahAdmisi = $this->makeDaftarPasien($user, 'RM-082');
        $kamar = Kamar::create(['klienfk' => $user->klienfk, 'nama' => 'Kamar 2', 'tarif_per_hari' => 100000, 'status' => 'kosong']);
        $this->actingAs($user)->post('/rawat-inap/admit', [
            'fk_daftarpasien' => $daftarSudahAdmisi->id,
            'fk_kamar' => $kamar->id,
        ]);

        $response = $this->actingAs($user)->post('/rawat-inap/getDaftarPasienOptions');

        $response->assertOk();
        $ids = collect($response->json('data'))->pluck('id');
        $this->assertTrue($ids->contains($daftarBelumAdmisi->id));
        $this->assertFalse($ids->contains($daftarSudahAdmisi->id));
    }

    public function test_billing_and_discharge_flow_end_to_end(): void
    {
        $user = $this->authorizedUser();
        $daftar = $this->makeDaftarPasien($user);
        $kamar = Kamar::create(['klienfk' => $user->klienfk, 'nama' => 'Kamar 3', 'tarif_per_hari' => 200000, 'status' => 'kosong']);
        $admit = $this->actingAs($user)->post('/rawat-inap/admit', [
            'fk_daftarpasien' => $daftar->id,
            'fk_kamar' => $kamar->id,
        ]);
        $admissionId = $admit->json('data.id');

        $billingResponse = $this->actingAs($user)->post("/rawat-inap/getBilling/{$admissionId}");
        $billingResponse->assertOk();
        $this->assertEquals(200000, $billingResponse->json('data.biaya_kamar'));

        $dischargeResponse = $this->actingAs($user)->post("/rawat-inap/discharge/{$admissionId}", [
            'ringkasan_pulang' => 'Sembuh, boleh pulang',
        ]);

        $dischargeResponse->assertOk();
        $this->assertDatabaseHas('t_rawatinap', ['id' => $admissionId, 'status' => 'keluar']);
        $this->assertSame('kosong', $kamar->refresh()->status);
    }

    public function test_can_add_and_list_daily_notes_for_an_active_admission(): void
    {
        $user = $this->authorizedUser();
        $daftar = $this->makeDaftarPasien($user);
        $kamar = Kamar::create(['klienfk' => $user->klienfk, 'nama' => 'Kamar 4', 'tarif_per_hari' => 100000, 'status' => 'kosong']);
        $admit = $this->actingAs($user)->post('/rawat-inap/admit', [
            'fk_daftarpasien' => $daftar->id,
            'fk_kamar' => $kamar->id,
        ]);
        $admissionId = $admit->json('data.id');

        $saveResponse = $this->actingAs($user)->post("/rawat-inap/saveCatatan/{$admissionId}", [
            'suhu' => '37.2',
            'nadi' => '80',
            'kesadaran' => 'Compos Mentis',
            'gcs' => 'E4V5M6',
            'spo2' => '98',
            'skala_nyeri' => '2',
            'intake_ml' => '500',
            'output_ml' => '350',
            'catatan' => 'Pasien stabil, tidak ada keluhan baru',
            'intervensi' => 'Observasi tanda vital dan edukasi pasien',
            'respons' => 'Pasien memahami edukasi',
        ]);
        $saveResponse->assertOk();

        $listResponse = $this->actingAs($user)->post("/rawat-inap/getCatatanList/{$admissionId}");
        $listResponse->assertOk();
        $this->assertCount(1, $listResponse->json('data'));
        $this->assertSame('Pasien stabil, tidak ada keluhan baru', $listResponse->json('data.0.catatan'));
        $this->assertSame('Compos Mentis', $listResponse->json('data.0.kesadaran'));
        $this->assertSame('E4V5M6', $listResponse->json('data.0.gcs'));
        $this->assertEquals(500, $listResponse->json('data.0.intake_ml'));
        $this->assertSame('Observasi tanda vital dan edukasi pasien', $listResponse->json('data.0.intervensi'));
    }

    public function test_can_create_list_and_evaluate_a_nursing_care_plan(): void
    {
        $user = $this->authorizedUser();
        $daftar = $this->makeDaftarPasien($user, 'RM-083');
        $kamar = Kamar::create([
            'klienfk' => $user->klienfk,
            'nama' => 'Kamar Care Plan',
            'tarif_per_hari' => 100000,
            'status' => 'kosong',
        ]);
        $admit = $this->actingAs($user)->post('/rawat-inap/admit', [
            'fk_daftarpasien' => $daftar->id,
            'fk_kamar' => $kamar->id,
        ]);
        $admissionId = $admit->json('data.id');

        $saveResponse = $this->actingAs($user)->post("/rawat-inap/saveCarePlan/{$admissionId}", [
            'diagnosis' => 'Nyeri akut',
            'goal' => 'Skala nyeri turun menjadi maksimal 3',
            'interventions' => 'Observasi nyeri dan lakukan teknik relaksasi',
            'target_date' => now()->addDay()->toDateString(),
        ]);
        $saveResponse->assertOk();
        $planId = $saveResponse->json('data.id');

        $listResponse = $this->actingAs($user)->post("/rawat-inap/getCarePlans/{$admissionId}");
        $listResponse->assertOk();
        $this->assertSame('Nyeri akut', $listResponse->json('data.0.diagnosis'));

        $evaluateResponse = $this->actingAs($user)->post("/rawat-inap/evaluateCarePlan/{$planId}", [
            'status' => 'teratasi',
            'evaluation' => 'Nyeri berkurang, pasien tampak nyaman',
        ]);
        $evaluateResponse->assertOk();
        $this->assertDatabaseHas('t_rawatinap_careplans', [
            'id' => $planId,
            'status' => 'teratasi',
            'evaluation' => 'Nyeri berkurang, pasien tampak nyaman',
        ]);
    }

    public function test_can_create_and_list_inpatient_medical_documents(): void
    {
        $user = $this->authorizedUser();
        $daftar = $this->makeDaftarPasien($user, 'RM-084');
        $kamar = Kamar::create([
            'klienfk' => $user->klienfk,
            'nama' => 'Kamar RM Inap',
            'tarif_per_hari' => 100000,
            'status' => 'kosong',
        ]);
        $admit = $this->actingAs($user)->post('/rawat-inap/admit', [
            'fk_daftarpasien' => $daftar->id,
            'fk_kamar' => $kamar->id,
        ]);
        $admissionId = $admit->json('data.id');

        $saveResponse = $this->actingAs($user)->post("/rawat-inap/saveMedicalRecord/{$admissionId}", [
            'type' => 'cppt',
            'subjective' => 'Pasien masih mengeluh nyeri',
            'objective' => 'Tampak meringis',
            'assessment' => 'Nyeri akut',
            'plan' => 'Observasi dan terapi sesuai instruksi',
        ]);
        $saveResponse->assertOk();

        $resumeResponse = $this->actingAs($user)->post("/rawat-inap/saveMedicalRecord/{$admissionId}", [
            'type' => 'resume_medis',
            'diagnosis_final' => 'Nyeri akut membaik',
            'procedure_summary' => 'Observasi dan terapi',
            'condition_discharge' => 'Stabil',
        ]);
        $resumeResponse->assertOk();

        $listResponse = $this->actingAs($user)->post("/rawat-inap/getMedicalRecords/{$admissionId}");
        $listResponse->assertOk();
        $this->assertCount(2, $listResponse->json('data'));
        $this->assertSame('resume_medis', $listResponse->json('data.0.type'));
        $this->assertSame('cppt', $listResponse->json('data.1.type'));
    }

    public function test_cannot_delete_a_room_that_is_currently_occupied(): void
    {
        $user = $this->authorizedUser();
        $daftar = $this->makeDaftarPasien($user);
        $kamar = Kamar::create(['klienfk' => $user->klienfk, 'nama' => 'Kamar 5', 'tarif_per_hari' => 100000, 'status' => 'kosong']);
        $this->actingAs($user)->post('/rawat-inap/admit', [
            'fk_daftarpasien' => $daftar->id,
            'fk_kamar' => $kamar->id,
        ]);

        $response = $this->actingAs($user)->post("/rawat-inap/deleteKamar/{$kamar->id}");

        $response->assertStatus(400);
        $this->assertDatabaseHas('m_kamar', ['id' => $kamar->id, 'deleted_at' => null]);
    }

    public function test_can_create_a_room_with_several_beds_via_http(): void
    {
        $user = $this->authorizedUser();

        $response = $this->actingAs($user)->post('/rawat-inap/saveRuangan', [
            'nama' => 'Ruang Mawar', 'kelas' => 'Kelas 1', 'tarif_per_hari' => 150000, 'jumlah_ranjang' => 3,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('m_ruangan_inap', ['nama' => 'Ruang Mawar', 'klienfk' => $user->klienfk]);
        $this->assertDatabaseCount('m_kamar', 3);
    }

    public function test_get_ruangan_list_reports_occupant_for_a_terisi_bed(): void
    {
        $user = $this->authorizedUser();
        $daftar = $this->makeDaftarPasien($user);
        $this->actingAs($user)->post('/rawat-inap/saveRuangan', [
            'nama' => 'Ruang Anggrek', 'tarif_per_hari' => 100000, 'jumlah_ranjang' => 1,
        ]);
        $bed = Kamar::where('nama', 'Ruang Anggrek - Ranjang 1')->first();

        $this->actingAs($user)->post('/rawat-inap/admit', [
            'fk_daftarpasien' => $daftar->id,
            'fk_kamar' => $bed->id,
        ]);

        $response = $this->actingAs($user)->post('/rawat-inap/getRuanganList');

        $response->assertOk();
        $beds = collect($response->json('data'))->firstWhere('nama', 'Ruang Anggrek')['beds'];
        $this->assertSame('terisi', $beds[0]['status']);
        $this->assertSame('Pasien RWI Controller', $beds[0]['pasien_nama']);
    }

    public function test_cannot_delete_a_room_with_an_occupied_bed_via_http(): void
    {
        $user = $this->authorizedUser();
        $daftar = $this->makeDaftarPasien($user);
        $this->actingAs($user)->post('/rawat-inap/saveRuangan', [
            'nama' => 'Ruang Dahlia', 'tarif_per_hari' => 100000, 'jumlah_ranjang' => 1,
        ]);
        $ruanganId = RuanganInap::where('nama', 'Ruang Dahlia')->first()->id;
        $bed = Kamar::where('nama', 'Ruang Dahlia - Ranjang 1')->first();
        $this->actingAs($user)->post('/rawat-inap/admit', [
            'fk_daftarpasien' => $daftar->id,
            'fk_kamar' => $bed->id,
        ]);

        $response = $this->actingAs($user)->post("/rawat-inap/deleteRuangan/{$ruanganId}");

        $response->assertStatus(400);
        $this->assertDatabaseHas('m_ruangan_inap', ['id' => $ruanganId, 'deleted_at' => null]);
    }
}
