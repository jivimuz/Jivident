<?php

namespace Tests\Feature;

use App\Models\DaftarPasien;
use App\Models\LabPasien;
use App\Models\Module;
use App\Models\Patient;
use App\Models\RadiologiPasien;
use App\Models\Serial;
use App\Models\TindakanPasien;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingControllerTest extends TestCase
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
        $user->access = (string) Module::where('code', 'BL')->value('id');
        $user->save();

        return $user;
    }

    /**
     * Regresi: endpoint ini sebelumnya SELALU gagal dengan SQL error
     * "Unknown column 'biaya_pendaftaran'" di setiap pemanggilan — kolom
     * yang benar adalah `biaya_registrasi`. Ditemukan saat menambahkan
     * tagihan lab/radiologi ke endpoint ini.
     */
    public function test_get_tagihan_list_does_not_crash_and_includes_registrasi(): void
    {
        $user = $this->authorizedUser();
        $patient = Patient::create(['klienfk' => $user->klienfk, 'norm' => 'RM090', 'nik' => '4040', 'nama' => 'Pasien Billing']);
        $daftar = DaftarPasien::create([
            'klienfk' => $user->klienfk, 'noregistrasi' => 'REG-BIL', 'ruanganfk' => 1, 'pasienfk' => $patient->id,
            'tglregistrasi' => now(), 'statuspasien' => 'LAMA', 'status' => 'Di Periksa', 'noantrian' => 'A1',
            'statuspembayaran' => 'Belum Verifikasi', 'biaya_registrasi' => 25000,
        ]);

        $response = $this->actingAs($user)->post('/billing/getTagihanList', [
            'daftarpasien_id' => $daftar->id,
        ]);

        $response->assertOk();
        $this->assertEquals(25000, $response->json('registrasi'));
    }

    public function test_get_tagihan_list_includes_lab_and_radiologi_charges(): void
    {
        $user = $this->authorizedUser();
        $patient = Patient::create(['klienfk' => $user->klienfk, 'norm' => 'RM091', 'nik' => '4041', 'nama' => 'Pasien Billing Lengkap']);
        $daftar = DaftarPasien::create([
            'klienfk' => $user->klienfk, 'noregistrasi' => 'REG-BIL2', 'ruanganfk' => 1, 'pasienfk' => $patient->id,
            'tglregistrasi' => now(), 'statuspasien' => 'LAMA', 'status' => 'Di Periksa', 'noantrian' => 'A2',
            'statuspembayaran' => 'Belum Verifikasi', 'biaya_registrasi' => 0,
        ]);
        TindakanPasien::create([
            'klienfk' => $user->klienfk, 'fk_daftarpasien' => $daftar->id, 'tindakan' => 'Scaling', 'dokter' => 'drg. A', 'qty' => 1, 'amount' => 50000,
        ]);
        LabPasien::create([
            'klienfk' => $user->klienfk, 'fk_daftarpasien' => $daftar->id, 'fk_pasien' => $patient->id,
            'jenis_nama' => 'Gula Darah', 'harga_snapshot' => 25000, 'status' => 'selesai', 'tgl_diminta' => now(),
        ]);
        RadiologiPasien::create([
            'klienfk' => $user->klienfk, 'fk_daftarpasien' => $daftar->id, 'fk_pasien' => $patient->id,
            'jenis_nama' => 'Panoramic', 'harga_snapshot' => 150000, 'status' => 'selesai', 'tgl_diminta' => now(),
        ]);

        $response = $this->actingAs($user)->post('/billing/getTagihanList', [
            'daftarpasien_id' => $daftar->id,
        ]);

        $response->assertOk();
        $this->assertCount(1, $response->json('tagihan_lab'));
        $this->assertEquals(25000, $response->json('tagihan_lab.0.amount'));
        $this->assertCount(1, $response->json('tagihan_radiologi'));
        $this->assertEquals(150000, $response->json('tagihan_radiologi.0.amount'));
    }

    /**
     * Regresi audit keamanan: getTagihanList/billingDetail/processPaid tidak
     * pernah men-scope lookup-nya ke klienfk — staf dari cabang lain bisa
     * lihat rincian tagihan, bahkan mencatat pembayaran atas nama cabang lain.
     */
    private function makeDaftarPasienInBranch(int $klienfk, string $noregistrasi): DaftarPasien
    {
        $patient = Patient::create(['klienfk' => $klienfk, 'norm' => 'RM-'.uniqid(), 'nik' => (string) random_int(1000000000, 9999999999), 'nama' => 'Pasien Cabang '.$klienfk]);

        return DaftarPasien::create([
            'klienfk' => $klienfk, 'noregistrasi' => $noregistrasi, 'ruanganfk' => 1, 'pasienfk' => $patient->id,
            'tglregistrasi' => now(), 'statuspasien' => 'LAMA', 'status' => 'Di Periksa', 'noantrian' => 'A1',
            'statuspembayaran' => 'Belum Verifikasi', 'biaya_registrasi' => 20000,
        ]);
    }

    public function test_cannot_view_tagihan_of_a_visit_in_another_branch(): void
    {
        $user = $this->authorizedUser();
        $daftarCabangLain = $this->makeDaftarPasienInBranch(2, 'REG-LAIN-1');

        $response = $this->actingAs($user)->post('/billing/getTagihanList', [
            'daftarpasien_id' => $daftarCabangLain->id,
        ]);

        $response->assertStatus(404);
    }

    public function test_cannot_open_billing_detail_of_a_visit_in_another_branch(): void
    {
        $user = $this->authorizedUser();
        $daftarCabangLain = $this->makeDaftarPasienInBranch(2, 'REG-LAIN-2');
        $encodedId = base64_encode(base64_encode((string) $daftarCabangLain->id));

        $response = $this->actingAs($user)->get("/billing/detail/{$encodedId}");

        $response->assertRedirect();
        $response->assertDontSee('REG-LAIN-2');
    }

    public function test_cannot_record_a_payment_against_a_visit_in_another_branch(): void
    {
        $user = $this->authorizedUser();
        $daftarCabangLain = $this->makeDaftarPasienInBranch(2, 'REG-LAIN-3');

        $response = $this->actingAs($user)->post('/billing/process-paid', [
            'fk_daftarpasien' => $daftarCabangLain->id,
            'total_tagihan' => 20000,
            'payment' => [['metode' => 1, 'nominal' => 20000]],
            'selisih' => 0,
        ]);

        $response->assertStatus(500);
        $this->assertStringContainsString('Daftar Pasien tidak ditemukan', $response->json('message'));
        $this->assertDatabaseMissing('t_transactions', ['daftarpasien_id' => $daftarCabangLain->id]);
    }
}
