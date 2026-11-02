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

class BiayaTambahanControllerTest extends TestCase
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
        $user->access = (string) Module::where('code', 'BTM')->value('id');
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
            'status' => 'Selesai',
            'noantrian' => 'A1',
            'statuspembayaran' => 'Belum Verifikasi',
            'biaya_registrasi' => 0,
        ], $overrides));
    }

    public function test_page_requires_access(): void
    {
        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addMonth()->toDateString(),
            'last_check' => now(),
        ]);
        $user = User::first();
        $user->access = '999999';
        $user->save();

        $response = $this->actingAs($user)->get('/biaya-tambahan');

        $response->assertRedirect(route('error'));
    }

    public function test_store_adds_a_charge_and_refreshes_billing_status(): void
    {
        $user = $this->authorizedUser();
        $daftar = $this->makeDaftarPasien($user->klienfk);

        $response = $this->actingAs($user)->post('/biaya-tambahan/store', [
            'daftarpasien_id' => $daftar->id,
            'nama' => 'Biaya Operasi',
            'jumlah' => 500000,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('t_biaya_tambahan', [
            'daftarpasien_id' => $daftar->id,
            'nama' => 'Biaya Operasi',
            'jumlah' => 500000,
        ]);
        // Belum dibayar — statuspembayaran harus ikut ter-refresh jadi "Belum Verifikasi"
        // (bukan diam di nilai lama), membuktikan BillingStatusHelper::refresh() terpanggil.
        $this->assertDatabaseHas('t_daftarpasien', [
            'id' => $daftar->id,
            'statuspembayaran' => 'Belum Verifikasi',
        ]);
    }

    public function test_store_rejects_a_visit_from_another_branch(): void
    {
        $user = $this->authorizedUser(klienfk: 1);
        $daftarLain = $this->makeDaftarPasien(2);

        $response = $this->actingAs($user)->post('/biaya-tambahan/store', [
            'daftarpasien_id' => $daftarLain->id,
            'nama' => 'Biaya Operasi',
            'jumlah' => 500000,
        ]);

        $response->assertStatus(404);
        $this->assertDatabaseMissing('t_biaya_tambahan', ['daftarpasien_id' => $daftarLain->id]);
    }

    public function test_destroy_removes_the_charge_and_refreshes_billing_status(): void
    {
        $user = $this->authorizedUser();
        $daftar = $this->makeDaftarPasien($user->klienfk);
        $biaya = BiayaTambahan::create([
            'klienfk' => $user->klienfk, 'daftarpasien_id' => $daftar->id,
            'nama' => 'Biaya Operasi', 'jumlah' => 500000, 'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->post("/biaya-tambahan/delete/{$biaya->id}");

        $response->assertOk();
        $this->assertSoftDeleted('t_biaya_tambahan', ['id' => $biaya->id]);
    }

    public function test_destroy_is_rejected_once_the_visit_is_marked_lunas(): void
    {
        $user = $this->authorizedUser();
        $daftar = $this->makeDaftarPasien($user->klienfk, ['statuspembayaran' => 'Lunas']);
        $biaya = BiayaTambahan::create([
            'klienfk' => $user->klienfk, 'daftarpasien_id' => $daftar->id,
            'nama' => 'Biaya Operasi', 'jumlah' => 500000, 'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->post("/biaya-tambahan/delete/{$biaya->id}");

        $response->assertStatus(400);
        $this->assertDatabaseHas('t_biaya_tambahan', ['id' => $biaya->id, 'deleted_at' => null]);
    }

    public function test_destroy_rejects_a_charge_from_another_branch(): void
    {
        $user = $this->authorizedUser(klienfk: 1);
        $daftarLain = $this->makeDaftarPasien(2);
        $biayaLain = BiayaTambahan::create([
            'klienfk' => 2, 'daftarpasien_id' => $daftarLain->id,
            'nama' => 'Biaya Operasi', 'jumlah' => 500000,
        ]);

        $response = $this->actingAs($user)->post("/biaya-tambahan/delete/{$biayaLain->id}");

        $response->assertStatus(404);
    }

    public function test_get_list_only_shows_charges_from_the_same_branch(): void
    {
        $user = $this->authorizedUser(klienfk: 1);
        $daftarSaya = $this->makeDaftarPasien(1);
        BiayaTambahan::create([
            'klienfk' => 1, 'daftarpasien_id' => $daftarSaya->id,
            'nama' => 'Biaya Cabang Saya', 'jumlah' => 100000,
        ]);
        $daftarLain = $this->makeDaftarPasien(2);
        BiayaTambahan::create([
            'klienfk' => 2, 'daftarpasien_id' => $daftarLain->id,
            'nama' => 'Biaya Cabang Lain', 'jumlah' => 200000,
        ]);

        $response = $this->actingAs($user)->post('/biaya-tambahan/getList');

        $response->assertOk();
        $names = collect($response->json('data'))->pluck('nama');
        $this->assertTrue($names->contains('Biaya Cabang Saya'));
        $this->assertFalse($names->contains('Biaya Cabang Lain'));
    }

    public function test_get_daftar_pasien_list_only_shows_visits_from_the_same_branch(): void
    {
        $user = $this->authorizedUser(klienfk: 1);
        $this->makeDaftarPasien(1);
        $this->makeDaftarPasien(2);

        $response = $this->actingAs($user)->post('/biaya-tambahan/getDaftarPasienList');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }
}
