<?php

namespace Tests\Feature;

use App\Helpers\RawatInapHelper;
use App\Models\DaftarPasien;
use App\Models\Kamar;
use App\Models\Module;
use App\Models\Patient;
use App\Models\RawatInap;
use App\Models\Serial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * "Daftar Rawat Inap" list page under the Daftar Pasien sidebar group —
 * read-only list of admissions, scoped by branch, filterable by status,
 * search by RM/nama. Admission itself happens elsewhere (Pemeriksaan tab
 * or /rawat-inap); this just lists and jumps into Pemeriksaan per row.
 */
class PasienRawatInapControllerTest extends TestCase
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
        $user->access = (string) Module::where('code', 'DPR')->value('id');
        $user->save();

        return $user;
    }

    private function makeAdmission(int $klienfk, string $norm, string $nama, string $status = 'dirawat'): RawatInap
    {
        $patient = Patient::create([
            'klienfk' => $klienfk, 'norm' => $norm, 'nik' => (string) random_int(1000000000, 9999999999), 'nama' => $nama,
        ]);

        $daftar = DaftarPasien::create([
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
        ]);

        $kamar = Kamar::create(['klienfk' => $klienfk, 'nama' => 'Kamar '.uniqid(), 'tarif_per_hari' => 100000, 'status' => 'kosong']);
        $admission = app(RawatInapHelper::class)->admit([
            'klienfk' => $klienfk,
            'fk_daftarpasien' => $daftar->id,
            'fk_kamar' => $kamar->id,
        ]);

        if ($status === 'keluar') {
            app(RawatInapHelper::class)->discharge($admission->id, ['klienfk' => $klienfk]);
        }

        return $admission;
    }

    public function test_page_requires_access(): void
    {
        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addMonth()->toDateString(),
            'last_check' => now(),
        ]);

        $response = $this->actingAs(User::first())->get('/pasien-rawat-inap');

        $response->assertStatus(302);
    }

    public function test_get_list_only_shows_active_admissions_from_the_same_branch(): void
    {
        $user = $this->authorizedUser(1);
        $this->makeAdmission(1, 'RM-001', 'Pasien Cabang Satu');
        $this->makeAdmission(2, 'RM-002', 'Pasien Cabang Dua');

        $response = $this->actingAs($user)->post('/pasien-rawat-inap/getList');

        $response->assertOk();
        $response->assertJsonFragment(['nama' => 'Pasien Cabang Satu']);
        $response->assertJsonMissing(['nama' => 'Pasien Cabang Dua']);
    }

    public function test_get_list_filters_by_status(): void
    {
        $user = $this->authorizedUser(1);
        $this->makeAdmission(1, 'RM-003', 'Pasien Masih Dirawat', 'dirawat');
        $this->makeAdmission(1, 'RM-004', 'Pasien Sudah Keluar', 'keluar');

        $dirawat = $this->actingAs($user)->post('/pasien-rawat-inap/getList', ['status' => 'dirawat']);
        $dirawat->assertJsonFragment(['nama' => 'Pasien Masih Dirawat']);
        $dirawat->assertJsonMissing(['nama' => 'Pasien Sudah Keluar']);

        $keluar = $this->actingAs($user)->post('/pasien-rawat-inap/getList', ['status' => 'keluar']);
        $keluar->assertJsonFragment(['nama' => 'Pasien Sudah Keluar']);
        $keluar->assertJsonMissing(['nama' => 'Pasien Masih Dirawat']);
    }

    public function test_get_list_search_matches_norm_or_nama(): void
    {
        $user = $this->authorizedUser(1);
        $this->makeAdmission(1, 'RM-UNIQUE-999', 'Budi Santoso');
        $this->makeAdmission(1, 'RM-005', 'Lainnya');

        $response = $this->actingAs($user)->post('/pasien-rawat-inap/getList', ['pasienSearch' => 'Budi']);

        $response->assertJsonFragment(['nama' => 'Budi Santoso']);
        $response->assertJsonMissing(['nama' => 'Lainnya']);
    }
}
