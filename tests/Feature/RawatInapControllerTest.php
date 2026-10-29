<?php

namespace Tests\Feature;

use App\Models\DaftarPasien;
use App\Models\Kamar;
use App\Models\Module;
use App\Models\Patient;
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
            'catatan' => 'Pasien stabil, tidak ada keluhan baru',
        ]);
        $saveResponse->assertOk();

        $listResponse = $this->actingAs($user)->post("/rawat-inap/getCatatanList/{$admissionId}");
        $listResponse->assertOk();
        $this->assertCount(1, $listResponse->json('data'));
        $this->assertSame('Pasien stabil, tidak ada keluhan baru', $listResponse->json('data.0.catatan'));
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
}
