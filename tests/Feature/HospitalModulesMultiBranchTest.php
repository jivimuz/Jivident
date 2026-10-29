<?php

namespace Tests\Feature;

use App\Models\DaftarPasien;
use App\Models\JenisPemeriksaanLab;
use App\Models\JenisPemeriksaanRadiologi;
use App\Models\Kamar;
use App\Models\Module;
use App\Models\Patient;
use App\Models\RawatInap;
use App\Models\Serial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Regresi: beberapa endpoint di modul IGD/Lab/Radiologi/Rawat Inap yang baru
 * dibangun ternyata tidak men-scope lookup-by-id ke `klienfk` milik user yang
 * login — artinya staf dari cabang lain bisa melihat/mengubah/menghapus data
 * cabang yang bukan miliknya hanya dengan menebak/mengirim ID lewat request
 * langsung (bukan lewat UI, yang memang sudah benar scoped). Ditemukan lewat
 * audit mandiri setelah ke-4 modul selesai, bukan dari laporan user.
 */
class HospitalModulesMultiBranchTest extends TestCase
{
    use RefreshDatabase;

    private function authorizedUser(string $moduleCode, int $klienfk): User
    {
        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addMonth()->toDateString(),
            'last_check' => now(),
        ]);

        $user = User::first();
        $user->klienfk = $klienfk;
        $user->access = (string) Module::where('code', $moduleCode)->value('id');
        $user->save();

        return $user;
    }

    public function test_cannot_delete_a_lab_master_test_belonging_to_another_branch(): void
    {
        $owner = $this->authorizedUser('LAB', 1);
        $jenis = JenisPemeriksaanLab::create(['klienfk' => 1, 'nama' => 'Punya Cabang 1', 'harga' => 10000]);

        $intruder = $this->authorizedUser('LAB', 2);
        $this->actingAs($intruder)->post("/lab/deleteMaster/{$jenis->id}");

        $this->assertDatabaseHas('m_jenispemeriksaanlab', ['id' => $jenis->id, 'deleted_at' => null]);
    }

    public function test_cannot_edit_a_radiologi_master_test_belonging_to_another_branch(): void
    {
        $owner = $this->authorizedUser('RAD', 1);
        $jenis = JenisPemeriksaanRadiologi::create(['klienfk' => 1, 'nama' => 'Panoramic Cabang 1', 'harga' => 100000]);

        $intruder = $this->authorizedUser('RAD', 2);
        $response = $this->actingAs($intruder)->postJson('/radiologi/saveMaster', [
            'id' => $jenis->id,
            'nama' => 'Diganti Cabang 2',
            'harga' => 999999,
        ]);

        $response->assertStatus(404);
        $this->assertDatabaseHas('m_jenispemeriksaanradiologi', ['id' => $jenis->id, 'nama' => 'Panoramic Cabang 1']);
    }

    public function test_cannot_delete_a_room_belonging_to_another_branch(): void
    {
        $owner = $this->authorizedUser('RWI', 1);
        $kamar = Kamar::create(['klienfk' => 1, 'nama' => 'Kamar Cabang 1', 'tarif_per_hari' => 100000, 'status' => 'kosong']);

        $intruder = $this->authorizedUser('RWI', 2);
        $this->actingAs($intruder)->post("/rawat-inap/deleteKamar/{$kamar->id}");

        $this->assertDatabaseHas('m_kamar', ['id' => $kamar->id, 'deleted_at' => null]);
    }

    public function test_admit_rejects_a_room_belonging_to_another_branch(): void
    {
        $this->authorizedUser('RWI', 1);
        $patient = Patient::create(['klienfk' => 1, 'norm' => 'RM-MB1', 'nik' => '5001', 'nama' => 'Pasien Cabang 1']);
        $daftar = DaftarPasien::create([
            'klienfk' => 1, 'noregistrasi' => 'REG-MB1', 'ruanganfk' => 1, 'pasienfk' => $patient->id,
            'tglregistrasi' => now(), 'statuspasien' => 'LAMA', 'status' => 'Di Periksa', 'noantrian' => 'A1',
            'statuspembayaran' => 'Belum Verifikasi', 'biaya_registrasi' => 0,
        ]);
        $kamarCabangLain = Kamar::create(['klienfk' => 2, 'nama' => 'Kamar Cabang 2', 'tarif_per_hari' => 100000, 'status' => 'kosong']);

        $intruder = $this->authorizedUser('RWI', 1);
        $response = $this->actingAs($intruder)->post('/rawat-inap/admit', [
            'fk_daftarpasien' => $daftar->id,
            'fk_kamar' => $kamarCabangLain->id,
        ]);

        $response->assertStatus(400);
        $this->assertDatabaseMissing('t_rawatinap', ['fk_daftarpasien' => $daftar->id]);
    }

    public function test_cannot_view_billing_of_an_admission_belonging_to_another_branch(): void
    {
        $patient = Patient::create(['klienfk' => 1, 'norm' => 'RM-MB2', 'nik' => '5002', 'nama' => 'Pasien Admisi Cabang 1']);
        $daftar = DaftarPasien::create([
            'klienfk' => 1, 'noregistrasi' => 'REG-MB2', 'ruanganfk' => 1, 'pasienfk' => $patient->id,
            'tglregistrasi' => now(), 'statuspasien' => 'LAMA', 'status' => 'Di Periksa', 'noantrian' => 'A1',
            'statuspembayaran' => 'Belum Verifikasi', 'biaya_registrasi' => 0,
        ]);
        $kamar = Kamar::create(['klienfk' => 1, 'nama' => 'Kamar 1', 'tarif_per_hari' => 100000, 'status' => 'terisi']);
        $admission = RawatInap::create([
            'klienfk' => 1, 'fk_daftarpasien' => $daftar->id, 'fk_pasien' => $patient->id, 'fk_kamar' => $kamar->id,
            'tgl_masuk' => now(), 'status' => 'dirawat',
        ]);

        $intruder = $this->authorizedUser('RWI', 2);
        $billingResponse = $this->actingAs($intruder)->post("/rawat-inap/getBilling/{$admission->id}");
        $dischargeResponse = $this->actingAs($intruder)->post("/rawat-inap/discharge/{$admission->id}");
        $catatanListResponse = $this->actingAs($intruder)->post("/rawat-inap/getCatatanList/{$admission->id}");
        $catatanSaveResponse = $this->actingAs($intruder)->post("/rawat-inap/saveCatatan/{$admission->id}", [
            'catatan' => 'Catatan palsu dari cabang lain',
        ]);

        $billingResponse->assertStatus(404);
        $dischargeResponse->assertStatus(404);
        $catatanListResponse->assertStatus(404);
        $catatanSaveResponse->assertStatus(404);
        $this->assertDatabaseHas('t_rawatinap', ['id' => $admission->id, 'status' => 'dirawat']);
        $this->assertDatabaseMissing('t_rawatinap_catatan', ['catatan' => 'Catatan palsu dari cabang lain']);
    }
}
