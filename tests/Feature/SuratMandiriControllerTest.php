<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Module;
use App\Models\Payments;
use App\Models\Serial;
use App\Models\Setting;
use App\Models\SuratMandiri;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuratMandiriControllerTest extends TestCase
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
        $user->access = (string) Module::where('code', 'SRT')->value('id');
        $user->save();

        return $user;
    }

    private function setHarga(int $value): void
    {
        Setting::where('parameter', 'patient_surat_keterangan_price')->update(['value' => $value]);
    }

    public function test_store_creates_surat_and_ledger_transaction_without_patient_link(): void
    {
        $user = $this->authorizedUser();
        $this->setHarga(25000);

        $dokter = Employee::create(['fullname' => 'drg. Mandiri', 'nik' => 'NIK-'.uniqid()]);
        $payment = Payments::first();

        $response = $this->actingAs($user)->post('/surat-mandiri/store', [
            'jenis' => 'sakit',
            'nama' => 'Budi Walk-in',
            'umur' => '30 Tahun',
            'fk_employee' => $dokter->id,
            'fk_payment' => $payment->id,
            'detail' => ['lama_istirahat' => 2, 'tanggal_mulai' => now()->toDateString()],
        ]);

        $response->assertOk();
        $response->assertJson(['message' => 'success', 'harga' => 25000]);

        $this->assertDatabaseHas('t_surat_mandiri', [
            'nama' => 'Budi Walk-in',
            'jenis' => 'sakit',
            'harga' => 25000,
            'klienfk' => 1,
        ]);

        $surat = SuratMandiri::first();
        $this->assertNotNull($surat->transaction_id);

        $this->assertDatabaseHas('t_transactions', [
            'id' => $surat->transaction_id,
            'typecode' => 'SRT',
            'amount' => 25000,
            'daftarpasien_id' => null,
        ]);

        // Ledger-only, no linked patient billing row should be created.
        $this->assertDatabaseCount('t_billingtransactions', 0);
    }

    public function test_store_does_not_create_transaction_when_price_is_zero(): void
    {
        $user = $this->authorizedUser();
        $this->setHarga(0);

        $dokter = Employee::create(['fullname' => 'drg. Gratis', 'nik' => 'NIK-'.uniqid()]);
        $payment = Payments::first();

        $response = $this->actingAs($user)->post('/surat-mandiri/store', [
            'jenis' => 'umum',
            'nama' => 'Pasien Gratis',
            'fk_employee' => $dokter->id,
            'fk_payment' => $payment->id,
            'detail' => ['judul_surat' => 'Surat Domisili', 'isi_surat' => 'Isi bebas'],
        ]);

        $response->assertOk();

        $surat = SuratMandiri::first();
        $this->assertNull($surat->transaction_id);
        $this->assertDatabaseCount('t_transactions', 0);
    }

    public function test_store_rejects_unknown_doctor(): void
    {
        $user = $this->authorizedUser();
        $this->setHarga(25000);
        $payment = Payments::first();

        $response = $this->actingAs($user)->post('/surat-mandiri/store', [
            'jenis' => 'sehat',
            'nama' => 'Tanpa Dokter',
            'fk_employee' => 999999,
            'fk_payment' => $payment->id,
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseCount('t_surat_mandiri', 0);
    }

    public function test_get_list_only_shows_letters_from_the_same_branch(): void
    {
        $owner = $this->authorizedUser(1);
        $this->setHarga(10000);
        $dokter = Employee::create(['fullname' => 'drg. A', 'nik' => 'NIK-'.uniqid()]);
        $payment = Payments::first();

        $this->actingAs($owner)->post('/surat-mandiri/store', [
            'jenis' => 'umum',
            'nama' => 'Milik Cabang 1',
            'fk_employee' => $dokter->id,
            'fk_payment' => $payment->id,
            'detail' => ['judul_surat' => 'X', 'isi_surat' => 'Y'],
        ]);

        $intruder = $this->authorizedUser(2);
        $response = $this->actingAs($intruder)->post('/surat-mandiri/getList');

        $response->assertOk();
        $this->assertSame(0, $response->json('recordsTotal'));
    }

    public function test_print_surat_mandiri_is_scoped_to_owning_branch(): void
    {
        $owner = $this->authorizedUser(1);
        $this->setHarga(10000);
        $dokter = Employee::create(['fullname' => 'drg. Cetak', 'nik' => 'NIK-'.uniqid()]);
        $payment = Payments::first();

        $this->actingAs($owner)->post('/surat-mandiri/store', [
            'jenis' => 'sehat',
            'nama' => 'Pasien Cetak',
            'fk_employee' => $dokter->id,
            'fk_payment' => $payment->id,
        ]);

        $surat = SuratMandiri::first();
        $encodedId = base64_encode(base64_encode((string) $surat->id));

        $intruder = $this->authorizedUser(2);
        $response = $this->actingAs($intruder)->get("/print/print-surat-mandiri/{$encodedId}");
        $response->assertNotFound();

        $response = $this->actingAs($owner)->get("/print/print-surat-mandiri/{$encodedId}");
        $response->assertOk();
        $response->assertSee('Pasien Cetak');
        $response->assertSee('drg. Cetak');
    }
}
