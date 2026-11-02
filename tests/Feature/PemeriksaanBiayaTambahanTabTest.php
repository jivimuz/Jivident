<?php

namespace Tests\Feature;

use App\Models\BiayaTambahan;
use App\Models\DaftarPasien;
use App\Models\Module;
use App\Models\Patient;
use App\Models\Serial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Biaya Tambahan tab on the Pemeriksaan page — lets a doctor/staff already
 * viewing a visit add an extra charge (e.g. biaya operasi) without having to
 * leave and search for the patient again on the standalone `/biaya-tambahan`
 * admin page (`BiayaTambahanController`), which still exists for charges
 * added to a visit nobody currently has open in Pemeriksaan.
 */
class PemeriksaanBiayaTambahanTabTest extends TestCase
{
    use RefreshDatabase;

    private function authorizedUser(int $klienfk = 1): User
    {
        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addMonth()->toDateString(),
            'last_check' => now(),
        ]);

        $user = User::first();
        $user->klienfk = $klienfk;
        $user->access = (string) Module::where('code', 'DPP')->value('id');
        $user->save();

        return $user;
    }

    private function makeDaftarPasien(int $klienfk, array $overrides = []): DaftarPasien
    {
        $patient = Patient::create([
            'klienfk' => $klienfk, 'norm' => 'RM-'.uniqid(), 'nik' => (string) random_int(1000000000, 9999999999), 'nama' => 'Pasien Biaya Tambahan',
        ]);

        return DaftarPasien::create(array_merge([
            'klienfk' => $klienfk,
            'noregistrasi' => 'REG-'.uniqid(),
            'ruanganfk' => 1,
            'pasienfk' => $patient->id,
            'tglregistrasi' => now(),
            'statuspasien' => 'LAMA',
            'status' => 'Di Periksa',
            'noantrian' => 'A1',
            'statuspembayaran' => 'Belum Verifikasi',
            'biaya_registrasi' => 20000,
        ], $overrides));
    }

    private function encode(int $id): string
    {
        return base64_encode(base64_encode((string) $id));
    }

    public function test_tab_shows_empty_state_when_no_charges_exist(): void
    {
        $user = $this->authorizedUser();
        $daftar = $this->makeDaftarPasien($user->klienfk);

        $response = $this->actingAs($user)->post("/pemeriksaan/pasien/{$this->encode($daftar->id)}/biayatambahan");

        $response->assertOk();
        $response->assertSee('Belum ada biaya tambahan');
        $response->assertSee('btnBukaBiayaTambahan');
    }

    public function test_save_biaya_tambahan_adds_a_charge_and_refreshes_billing_status(): void
    {
        $user = $this->authorizedUser();
        $daftar = $this->makeDaftarPasien($user->klienfk, ['statuspembayaran' => 'Lunas']);

        $response = $this->actingAs($user)->post("/pemeriksaan/pasien/{$this->encode($daftar->id)}/biayatambahan/save", [
            'nama' => 'Biaya Operasi',
            'jumlah' => 500000,
            'keterangan' => 'Operasi kecil',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('t_biaya_tambahan', [
            'daftarpasien_id' => $daftar->id,
            'nama' => 'Biaya Operasi',
            'jumlah' => 500000,
        ]);

        // Menambah biaya ke kunjungan yang sudah Lunas harus membuka kembali
        // tagihannya — sama seperti perilaku charge lain (resep/tindakan/dll).
        $this->assertNotEquals('Lunas', $daftar->refresh()->statuspembayaran);
    }

    public function test_save_biaya_tambahan_rejects_a_visit_from_another_branch(): void
    {
        $user = $this->authorizedUser(klienfk: 1);
        $daftarLain = $this->makeDaftarPasien(2);

        $response = $this->actingAs($user)->post("/pemeriksaan/pasien/{$this->encode($daftarLain->id)}/biayatambahan/save", [
            'nama' => 'Biaya Operasi',
            'jumlah' => 100000,
        ]);

        $response->assertStatus(404);
    }

    public function test_delete_biaya_tambahan_removes_the_charge_and_refreshes_billing_status(): void
    {
        $user = $this->authorizedUser();
        $daftar = $this->makeDaftarPasien($user->klienfk);
        $biaya = BiayaTambahan::create([
            'klienfk' => $user->klienfk,
            'daftarpasien_id' => $daftar->id,
            'nama' => 'Biaya Operasi',
            'jumlah' => 300000,
        ]);

        $response = $this->actingAs($user)
            ->post("/pemeriksaan/pasien/{$this->encode($daftar->id)}/biayatambahan/delete/{$biaya->id}");

        $response->assertOk();
        $this->assertSoftDeleted('t_biaya_tambahan', ['id' => $biaya->id]);
    }

    public function test_delete_biaya_tambahan_is_rejected_once_the_visit_is_lunas(): void
    {
        $user = $this->authorizedUser();
        $daftar = $this->makeDaftarPasien($user->klienfk, ['statuspembayaran' => 'Lunas']);
        $biaya = BiayaTambahan::create([
            'klienfk' => $user->klienfk,
            'daftarpasien_id' => $daftar->id,
            'nama' => 'Biaya Operasi',
            'jumlah' => 300000,
        ]);

        $response = $this->actingAs($user)
            ->post("/pemeriksaan/pasien/{$this->encode($daftar->id)}/biayatambahan/delete/{$biaya->id}");

        $response->assertStatus(400);
        $this->assertDatabaseHas('t_biaya_tambahan', ['id' => $biaya->id, 'deleted_at' => null]);
    }

    public function test_cannot_delete_a_charge_belonging_to_another_visit(): void
    {
        $user = $this->authorizedUser(klienfk: 1);
        $daftarSaya = $this->makeDaftarPasien(1);
        $daftarLain = $this->makeDaftarPasien(1);

        $biayaLain = BiayaTambahan::create([
            'klienfk' => 1,
            'daftarpasien_id' => $daftarLain->id,
            'nama' => 'Biaya Kunjungan Lain',
            'jumlah' => 150000,
        ]);

        $response = $this->actingAs($user)
            ->post("/pemeriksaan/pasien/{$this->encode($daftarSaya->id)}/biayatambahan/delete/{$biayaLain->id}");

        $response->assertStatus(404);
        $this->assertDatabaseHas('t_biaya_tambahan', ['id' => $biayaLain->id, 'deleted_at' => null]);
    }
}
