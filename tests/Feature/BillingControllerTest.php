<?php

namespace Tests\Feature;

use App\Models\BiayaTambahan;
use App\Models\DaftarPasien;
use App\Models\LabPasien;
use App\Models\Module;
use App\Models\Patient;
use App\Models\RadiologiPasien;
use App\Models\ResepPasien;
use App\Models\Serial;
use App\Models\TindakanPasien;
use App\Models\Transactions;
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
     * Regresi: t_reseppasien.subtotal tidak pernah ikut tagihan pasien sama
     * sekali sebelumnya (FK-nya `daftarpasien_id`, bukan `fk_daftarpasien`
     * seperti tindakan/diagnosa/lab/radiologi, jadi mudah terlewat).
     */
    public function test_get_tagihan_list_includes_resep_charges(): void
    {
        $user = $this->authorizedUser();
        $patient = Patient::create(['klienfk' => $user->klienfk, 'norm' => 'RM092', 'nik' => '4042', 'nama' => 'Pasien Resep']);
        $daftar = DaftarPasien::create([
            'klienfk' => $user->klienfk, 'noregistrasi' => 'REG-BIL3', 'ruanganfk' => 1, 'pasienfk' => $patient->id,
            'tglregistrasi' => now(), 'statuspasien' => 'LAMA', 'status' => 'Di Periksa', 'noantrian' => 'A3',
            'statuspembayaran' => 'Belum Verifikasi', 'biaya_registrasi' => 0,
        ]);
        ResepPasien::create([
            'klienfk' => $user->klienfk, 'pasien_id' => $patient->id, 'daftarpasien_id' => $daftar->id,
            'type' => 'non-racik', 'r_num' => 1, 'obat_name' => 'Paracetamol', 'qty' => 10,
            'harga_satuan' => 2000, 'unit' => 'Tablet', 'penggunaan_id' => 1, 'subtotal' => 20000,
        ]);

        $response = $this->actingAs($user)->post('/billing/getTagihanList', [
            'daftarpasien_id' => $daftar->id,
        ]);

        $response->assertOk();
        $this->assertCount(1, $response->json('tagihan_resep'));
        $this->assertEquals(20000, $response->json('tagihan_resep.0.amount'));
        $this->assertEquals('Paracetamol', $response->json('tagihan_resep.0.nama'));
    }

    public function test_get_tagihan_list_includes_biaya_tambahan(): void
    {
        $user = $this->authorizedUser();
        $patient = Patient::create(['klienfk' => $user->klienfk, 'norm' => 'RM093', 'nik' => '4043', 'nama' => 'Pasien Biaya Lain']);
        $daftar = DaftarPasien::create([
            'klienfk' => $user->klienfk, 'noregistrasi' => 'REG-BIL4', 'ruanganfk' => 1, 'pasienfk' => $patient->id,
            'tglregistrasi' => now(), 'statuspasien' => 'LAMA', 'status' => 'Di Periksa', 'noantrian' => 'A4',
            'statuspembayaran' => 'Belum Verifikasi', 'biaya_registrasi' => 0,
        ]);
        BiayaTambahan::create([
            'klienfk' => $user->klienfk, 'daftarpasien_id' => $daftar->id,
            'nama' => 'Biaya Operasi', 'jumlah' => 750000,
        ]);

        $response = $this->actingAs($user)->post('/billing/getTagihanList', [
            'daftarpasien_id' => $daftar->id,
        ]);

        $response->assertOk();
        $this->assertCount(1, $response->json('tagihan_lain'));
        $this->assertEquals(750000, $response->json('tagihan_lain.0.amount'));
        $this->assertEquals('Biaya Operasi', $response->json('tagihan_lain.0.nama'));
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

    /**
     * "Lunas" tidak pernah bisa dicapai lewat alur mana pun sebelumnya —
     * markLunas() adalah langkah penutupan manual yang disengaja, terpisah
     * dari processPaid() (lihat catatan di BillingController::markLunas()).
     */
    public function test_mark_lunas_closes_a_fully_paid_visit(): void
    {
        $user = $this->authorizedUser();
        $patient = Patient::create(['klienfk' => $user->klienfk, 'norm' => 'RM094', 'nik' => '4044', 'nama' => 'Pasien Lunas']);
        $daftar = DaftarPasien::create([
            'klienfk' => $user->klienfk, 'noregistrasi' => 'REG-LUNAS1', 'ruanganfk' => 1, 'pasienfk' => $patient->id,
            'tglregistrasi' => now(), 'statuspasien' => 'LAMA', 'status' => 'Selesai', 'noantrian' => 'A5',
            'statuspembayaran' => 'Verifikasi', 'biaya_registrasi' => 20000,
        ]);
        Transactions::create([
            'klienfk' => $user->klienfk, 'no_transaction' => 'TRX-LUNAS1', 'daftarpasien_id' => $daftar->id,
            'transaction_type' => 'in', 'typecode' => 'BIL', 'amount' => 20000,
        ]);

        $encodedId = base64_encode(base64_encode((string) $daftar->id));
        $response = $this->actingAs($user)->post("/billing/mark-lunas/{$encodedId}");

        $response->assertOk();
        $this->assertDatabaseHas('t_daftarpasien', ['id' => $daftar->id, 'statuspembayaran' => 'Lunas']);
    }

    public function test_mark_lunas_rejects_a_visit_that_is_not_fully_paid(): void
    {
        $user = $this->authorizedUser();
        $patient = Patient::create(['klienfk' => $user->klienfk, 'norm' => 'RM095', 'nik' => '4045', 'nama' => 'Pasien Belum Lunas']);
        $daftar = DaftarPasien::create([
            'klienfk' => $user->klienfk, 'noregistrasi' => 'REG-LUNAS2', 'ruanganfk' => 1, 'pasienfk' => $patient->id,
            'tglregistrasi' => now(), 'statuspasien' => 'LAMA', 'status' => 'Selesai', 'noantrian' => 'A6',
            'statuspembayaran' => 'Belum Verifikasi', 'biaya_registrasi' => 20000,
        ]);

        $encodedId = base64_encode(base64_encode((string) $daftar->id));
        $response = $this->actingAs($user)->post("/billing/mark-lunas/{$encodedId}");

        $response->assertStatus(400);
        $this->assertDatabaseHas('t_daftarpasien', ['id' => $daftar->id, 'statuspembayaran' => 'Belum Verifikasi']);
    }

    public function test_mark_lunas_rejects_a_visit_already_lunas(): void
    {
        $user = $this->authorizedUser();
        $patient = Patient::create(['klienfk' => $user->klienfk, 'norm' => 'RM096', 'nik' => '4046', 'nama' => 'Pasien Sudah Lunas']);
        $daftar = DaftarPasien::create([
            'klienfk' => $user->klienfk, 'noregistrasi' => 'REG-LUNAS3', 'ruanganfk' => 1, 'pasienfk' => $patient->id,
            'tglregistrasi' => now(), 'statuspasien' => 'LAMA', 'status' => 'Selesai', 'noantrian' => 'A7',
            'statuspembayaran' => 'Lunas', 'biaya_registrasi' => 20000,
        ]);

        $encodedId = base64_encode(base64_encode((string) $daftar->id));
        $response = $this->actingAs($user)->post("/billing/mark-lunas/{$encodedId}");

        $response->assertStatus(400);
    }

    public function test_mark_lunas_rejects_a_visit_from_another_branch(): void
    {
        $user = $this->authorizedUser();
        $daftarCabangLain = $this->makeDaftarPasienInBranch(2, 'REG-LUNAS4');
        $encodedId = base64_encode(base64_encode((string) $daftarCabangLain->id));

        $response = $this->actingAs($user)->post("/billing/mark-lunas/{$encodedId}");

        $response->assertStatus(404);
    }
}
