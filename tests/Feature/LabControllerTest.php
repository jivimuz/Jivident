<?php

namespace Tests\Feature;

use App\Models\DaftarPasien;
use App\Models\JenisPemeriksaanLab;
use App\Models\LabPasien;
use App\Models\Module;
use App\Models\Patient;
use App\Models\Serial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LabControllerTest extends TestCase
{
    use RefreshDatabase;

    private function authorizedUser(string $moduleCode): User
    {
        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addMonth()->toDateString(),
            'last_check' => now(),
        ]);

        $user = User::first();
        $user->access = (string) Module::where('code', $moduleCode)->value('id');
        $user->save();

        return $user;
    }

    private function makeDaftarPasien(User $user, Patient $patient): DaftarPasien
    {
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

    public function test_lab_page_requires_access(): void
    {
        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addMonth()->toDateString(),
            'last_check' => now(),
        ]);
        $user = User::first();
        $user->access = '999999';
        $user->save();

        $response = $this->actingAs($user)->get('/lab');

        $response->assertRedirect(route('error'));
    }

    public function test_can_create_a_master_jenis_pemeriksaan(): void
    {
        $user = $this->authorizedUser('LAB');

        $response = $this->actingAs($user)->post('/lab/saveMaster', [
            'nama' => 'Hemoglobin',
            'satuan' => 'g/dL',
            'nilai_rujukan' => '12-16',
            'harga' => 35000,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('m_jenispemeriksaanlab', ['nama' => 'Hemoglobin', 'harga' => 35000]);
    }

    public function test_doctor_can_request_lab_test_from_pemeriksaan_tab(): void
    {
        $user = $this->authorizedUser('DPP');
        $patient = Patient::create(['klienfk' => $user->klienfk, 'norm' => 'RM060', 'nik' => '2020', 'nama' => 'Pasien Lab']);
        $daftar = $this->makeDaftarPasien($user, $patient);
        $jenis = JenisPemeriksaanLab::create(['klienfk' => $user->klienfk, 'nama' => 'Gula Darah', 'satuan' => 'mg/dL', 'nilai_rujukan' => '70-110', 'harga' => 25000]);
        $encodedId = base64_encode(base64_encode((string) $daftar->id));

        $response = $this->actingAs($user)->post("/pemeriksaan/pasien/{$encodedId}/lab/save", [
            'fk_jenispemeriksaan' => $jenis->id,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('t_labpasien', [
            'fk_daftarpasien' => $daftar->id,
            'jenis_nama' => 'Gula Darah',
            'nilai_rujukan_snapshot' => '70-110',
            'status' => 'diminta',
        ]);
    }

    public function test_lab_order_snapshot_survives_master_data_changes(): void
    {
        $user = $this->authorizedUser('DPP');
        $patient = Patient::create(['klienfk' => $user->klienfk, 'norm' => 'RM061', 'nik' => '2021', 'nama' => 'Pasien Snapshot']);
        $daftar = $this->makeDaftarPasien($user, $patient);
        $jenis = JenisPemeriksaanLab::create(['klienfk' => $user->klienfk, 'nama' => 'Kolesterol', 'nilai_rujukan' => '<200', 'harga' => 40000]);
        $encodedId = base64_encode(base64_encode((string) $daftar->id));

        $this->actingAs($user)->post("/pemeriksaan/pasien/{$encodedId}/lab/save", ['fk_jenispemeriksaan' => $jenis->id]);
        $order = LabPasien::first();

        $jenis->update(['nama' => 'Kolesterol Total (diubah)', 'nilai_rujukan' => '<190']);

        $this->assertSame('Kolesterol', $order->refresh()->jenis_nama);
        $this->assertSame('<200', $order->nilai_rujukan_snapshot);
    }

    public function test_lab_staff_can_input_result_and_mark_abnormal(): void
    {
        $user = $this->authorizedUser('LAB');
        $patient = Patient::create(['klienfk' => $user->klienfk, 'norm' => 'RM062', 'nik' => '2022', 'nama' => 'Pasien Hasil']);
        $daftar = $this->makeDaftarPasien($user, $patient);
        $order = LabPasien::create([
            'klienfk' => $user->klienfk,
            'fk_daftarpasien' => $daftar->id,
            'fk_pasien' => $patient->id,
            'jenis_nama' => 'Hemoglobin',
            'satuan_snapshot' => 'g/dL',
            'status' => 'diminta',
            'tgl_diminta' => now(),
        ]);

        $response = $this->actingAs($user)->post("/lab/saveHasil/{$order->id}", [
            'hasil' => '9.5',
            'hasil_abnormal' => true,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('t_labpasien', [
            'id' => $order->id,
            'hasil' => '9.5',
            'hasil_abnormal' => true,
            'status' => 'selesai',
        ]);
    }

    public function test_worklist_only_shows_pending_orders_by_default(): void
    {
        $user = $this->authorizedUser('LAB');
        $patient = Patient::create(['klienfk' => $user->klienfk, 'norm' => 'RM063', 'nik' => '2023', 'nama' => 'Pasien Worklist']);
        $daftar = $this->makeDaftarPasien($user, $patient);

        LabPasien::create([
            'klienfk' => $user->klienfk, 'fk_daftarpasien' => $daftar->id, 'fk_pasien' => $patient->id,
            'jenis_nama' => 'Pending Test', 'status' => 'diminta', 'tgl_diminta' => now(),
        ]);
        LabPasien::create([
            'klienfk' => $user->klienfk, 'fk_daftarpasien' => $daftar->id, 'fk_pasien' => $patient->id,
            'jenis_nama' => 'Done Test', 'status' => 'selesai', 'tgl_diminta' => now(), 'tgl_hasil' => now(),
        ]);

        $response = $this->actingAs($user)->post('/lab/getWorklist');

        $response->assertOk();
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertSame('Pending Test', $data[0]['jenis_nama']);
    }
}
