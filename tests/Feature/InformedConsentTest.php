<?php

namespace Tests\Feature;

use App\Models\DaftarPasien;
use App\Models\InformedConsent;
use App\Models\Module;
use App\Models\Patient;
use App\Models\Serial;
use App\Models\Tindakan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InformedConsentTest extends TestCase
{
    use RefreshDatabase;

    // 1x1 transparent PNG.
    private const SAMPLE_PNG_BASE64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=';

    private function authorizedUser(): User
    {
        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addMonth()->toDateString(),
            'last_check' => now(),
        ]);

        $user = User::first();
        $user->access = (string) Module::where('code', 'DPP')->value('id');
        $user->save();

        return $user;
    }

    private function makeDaftarPasien(User $user, Patient $patient, array $overrides = []): DaftarPasien
    {
        return DaftarPasien::create(array_merge([
            'klienfk' => $user->klienfk,
            'noregistrasi' => 'REG-'.uniqid(),
            'ruanganfk' => 1,
            'pasienfk' => $patient->id,
            'tglregistrasi' => now(),
            'statuspasien' => 'BARU',
            'status' => 'Di Periksa',
            'noantrian' => 'A1',
            'statuspembayaran' => 'Belum Verifikasi',
            'biaya_registrasi' => 20000,
        ], $overrides));
    }

    protected function tearDown(): void
    {
        foreach (glob(public_path('images/consent-signatures/*')) ?: [] as $file) {
            @unlink($file);
        }

        parent::tearDown();
    }

    public function test_consent_tab_only_lists_tindakan_flagged_butuh_consent(): void
    {
        $user = $this->authorizedUser();
        $patient = Patient::create(['klienfk' => $user->klienfk, 'norm' => 'RM050', 'nik' => '8888', 'nama' => 'Pasien Consent']);
        $daftar = $this->makeDaftarPasien($user, $patient);

        Tindakan::create(['klienfk' => $user->klienfk, 'name' => 'Cabut Gigi', 'amount' => 100000, 'butuh_consent' => true]);
        Tindakan::create(['klienfk' => $user->klienfk, 'name' => 'Scaling', 'amount' => 50000, 'butuh_consent' => false]);

        $encodedId = base64_encode(base64_encode((string) $daftar->id));
        $response = $this->actingAs($user)->post("/pemeriksaan/pasien/{$encodedId}/consent");

        $response->assertOk();
        $response->assertSee('Cabut Gigi');
        $response->assertDontSee('Scaling');
    }

    public function test_can_save_a_general_consent(): void
    {
        $user = $this->authorizedUser();
        $patient = Patient::create(['klienfk' => $user->klienfk, 'norm' => 'RM051', 'nik' => '9999', 'nama' => 'Pasien Umum']);
        $daftar = $this->makeDaftarPasien($user, $patient);
        $encodedId = base64_encode(base64_encode((string) $daftar->id));

        $response = $this->actingAs($user)->post("/pemeriksaan/pasien/{$encodedId}/consent/save", [
            'tipe' => 'general',
            'ditandatangani_oleh' => 'Pasien Umum',
            'hubungan_dengan_pasien' => 'Pasien Sendiri',
            'signature' => self::SAMPLE_PNG_BASE64,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('t_informed_consent', [
            'fk_daftarpasien' => $daftar->id,
            'fk_pasien' => $patient->id,
            'tipe' => 'general',
            'ditandatangani_oleh' => 'Pasien Umum',
        ]);
        $consent = InformedConsent::first();
        $this->assertStringContainsString('menerima penjelasan mengenai prosedur pelayanan kesehatan gigi', $consent->isi_consent);
        $this->assertFileExists(public_path($consent->signature_path));
    }

    public function test_can_save_a_tindakan_specific_consent_with_snapshot_text(): void
    {
        $user = $this->authorizedUser();
        $patient = Patient::create(['klienfk' => $user->klienfk, 'norm' => 'RM052', 'nik' => '1010', 'nama' => 'Pasien Cabut']);
        $daftar = $this->makeDaftarPasien($user, $patient);
        $tindakan = Tindakan::create(['klienfk' => $user->klienfk, 'name' => 'Cabut Gigi Bungsu', 'amount' => 300000, 'butuh_consent' => true]);
        $encodedId = base64_encode(base64_encode((string) $daftar->id));

        $response = $this->actingAs($user)->post("/pemeriksaan/pasien/{$encodedId}/consent/save", [
            'tipe' => 'tindakan',
            'tindakan_id' => $tindakan->id,
            'ditandatangani_oleh' => 'Orang Tua Pasien',
            'hubungan_dengan_pasien' => 'Orang Tua',
            'signature' => self::SAMPLE_PNG_BASE64,
        ]);

        $response->assertOk();
        $consent = InformedConsent::first();
        $this->assertSame('tindakan', $consent->tipe);
        $this->assertSame('Cabut Gigi Bungsu', $consent->tindakan_nama);
        $this->assertStringContainsString('Cabut Gigi Bungsu', $consent->isi_consent);

        // Snapshot ikut berubah bentuk kalau master tindakan diubah/dihapus setelahnya —
        // riwayat consent tetap utuh karena nama disalin, bukan direferensikan ulang.
        $tindakan->delete();
        $this->assertSame('Cabut Gigi Bungsu', $consent->refresh()->tindakan_nama);
    }

    public function test_save_consent_requires_tindakan_id_when_tipe_is_tindakan(): void
    {
        $user = $this->authorizedUser();
        $patient = Patient::create(['klienfk' => $user->klienfk, 'norm' => 'RM053', 'nik' => '1212', 'nama' => 'Pasien Tanpa Tindakan']);
        $daftar = $this->makeDaftarPasien($user, $patient);
        $encodedId = base64_encode(base64_encode((string) $daftar->id));

        $response = $this->actingAs($user)->postJson("/pemeriksaan/pasien/{$encodedId}/consent/save", [
            'tipe' => 'tindakan',
            'ditandatangani_oleh' => 'Pasien',
            'hubungan_dengan_pasien' => 'Pasien Sendiri',
            'signature' => self::SAMPLE_PNG_BASE64,
        ]);

        $response->assertStatus(422);
    }

    public function test_save_consent_rejects_invalid_signature_format(): void
    {
        $user = $this->authorizedUser();
        $patient = Patient::create(['klienfk' => $user->klienfk, 'norm' => 'RM054', 'nik' => '1313', 'nama' => 'Pasien Sig Invalid']);
        $daftar = $this->makeDaftarPasien($user, $patient);
        $encodedId = base64_encode(base64_encode((string) $daftar->id));

        $response = $this->actingAs($user)->postJson("/pemeriksaan/pasien/{$encodedId}/consent/save", [
            'tipe' => 'general',
            'ditandatangani_oleh' => 'Pasien',
            'hubungan_dengan_pasien' => 'Pasien Sendiri',
            'signature' => 'not-a-png',
        ]);

        $response->assertStatus(400);
    }

    public function test_consent_history_is_scoped_to_patient_across_visits_not_just_one_visit(): void
    {
        $user = $this->authorizedUser();
        $patient = Patient::create(['klienfk' => $user->klienfk, 'norm' => 'RM055', 'nik' => '1414', 'nama' => 'Pasien Berulang']);
        $firstVisit = $this->makeDaftarPasien($user, $patient, ['noregistrasi' => 'REG-FIRST']);
        $secondVisit = $this->makeDaftarPasien($user, $patient, ['noregistrasi' => 'REG-SECOND']);

        $firstEncoded = base64_encode(base64_encode((string) $firstVisit->id));
        $this->actingAs($user)->post("/pemeriksaan/pasien/{$firstEncoded}/consent/save", [
            'tipe' => 'general',
            'ditandatangani_oleh' => 'Pasien Berulang',
            'hubungan_dengan_pasien' => 'Pasien Sendiri',
            'signature' => self::SAMPLE_PNG_BASE64,
        ]);

        $secondEncoded = base64_encode(base64_encode((string) $secondVisit->id));
        $response = $this->actingAs($user)->post("/pemeriksaan/pasien/{$secondEncoded}/consent/getList");

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    public function test_print_consent_shows_signature_and_matching_template_text(): void
    {
        $user = $this->authorizedUser();
        $patient = Patient::create(['klienfk' => $user->klienfk, 'norm' => 'RM056', 'nik' => '1515', 'nama' => 'Pasien Cetak Consent']);
        $daftar = $this->makeDaftarPasien($user, $patient);
        $encodedId = base64_encode(base64_encode((string) $daftar->id));

        $this->actingAs($user)->post("/pemeriksaan/pasien/{$encodedId}/consent/save", [
            'tipe' => 'general',
            'ditandatangani_oleh' => 'Pasien Cetak Consent',
            'hubungan_dengan_pasien' => 'Pasien Sendiri',
            'signature' => self::SAMPLE_PNG_BASE64,
        ]);
        $consent = InformedConsent::first();
        $consentEncodedId = base64_encode(base64_encode((string) $consent->id));

        $response = $this->actingAs($user)->get("/print/print-consent/{$consentEncodedId}");

        $response->assertOk();
        $response->assertSee('PERSETUJUAN UMUM PELAYANAN');
        $response->assertSee('Pasien Cetak Consent');
        $response->assertSee($consent->signature_path, false);
    }
}
