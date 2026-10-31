<?php

namespace Tests\Feature;

use App\Helpers\RawatInapHelper;
use App\Models\DaftarPasien;
use App\Models\Kamar;
use App\Models\Module;
use App\Models\Patient;
use App\Models\Serial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Regresi: "Selesai" sebelumnya hanya tercapai setelah kunjungan lunas —
 * PemeriksaanController::updateStatus() menahan status di 'Payment' sampai
 * statuspembayaran jadi 'Verifikasi'/'Lunas'. Developer sendiri menandai ini
 * sebagai ambiguitas yang belum diputuskan lewat komentar di
 * BillingController::processPaid(). Sekarang "selesaikan pemeriksaan" dan
 * "sudah lunas" adalah dua hal independen: status klinis vs statuspembayaran.
 */
class PemeriksaanUpdateStatusTest extends TestCase
{
    use RefreshDatabase;

    private function authorizedUser(array $moduleCodes): User
    {
        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addMonth()->toDateString(),
            'last_check' => now(),
        ]);

        $ids = Module::whereIn('code', $moduleCodes)->pluck('id');

        $user = User::first();
        $user->access = $ids->implode(',');
        $user->save();

        return $user;
    }

    private function makeDaftarPasien(User $user, array $overrides = []): DaftarPasien
    {
        $patient = Patient::create([
            'klienfk' => $user->klienfk, 'norm' => 'RM-'.uniqid(), 'nik' => (string) random_int(1000000000, 9999999999), 'nama' => 'Pasien Status Test',
        ]);

        return DaftarPasien::create(array_merge([
            'klienfk' => $user->klienfk,
            'noregistrasi' => 'REG-'.uniqid(),
            'ruanganfk' => 1,
            'pasienfk' => $patient->id,
            'tglregistrasi' => now(),
            'statuspasien' => 'LAMA',
            'status' => 'Di Periksa',
            'noantrian' => 'A1',
            'statuspembayaran' => 'Belum Verifikasi',
            'biaya_registrasi' => 50000,
        ], $overrides));
    }

    private function encode(int $id): string
    {
        return base64_encode(base64_encode((string) $id));
    }

    public function test_finishing_examination_sets_status_selesai_even_when_unpaid(): void
    {
        $user = $this->authorizedUser(['DPP']);
        $daftar = $this->makeDaftarPasien($user);

        $response = $this->actingAs($user)->post("/pemeriksaan/pasien/{$this->encode($daftar->id)}/updateStatus", [
            'f' => 1,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('t_daftarpasien', ['id' => $daftar->id, 'status' => 'Selesai']);
        // Belum ada pembayaran sama sekali -> statuspembayaran tetap apa adanya,
        // bukan ikut jadi "Lunas" hanya karena pemeriksaan ditutup.
        $this->assertDatabaseMissing('t_daftarpasien', ['id' => $daftar->id, 'statuspembayaran' => 'Lunas']);
    }

    public function test_finished_unpaid_visit_still_appears_in_unpaid_billing_list(): void
    {
        $user = $this->authorizedUser(['DPP', 'BL']);
        $daftar = $this->makeDaftarPasien($user);
        $this->actingAs($user)->post("/pemeriksaan/pasien/{$this->encode($daftar->id)}/updateStatus", ['f' => 1]);

        $response = $this->actingAs($user)->post('/billing/getList', [
            'status_pembayaran' => 'Not Yet',
        ]);

        $response->assertOk();
        $ids = collect($response->json('data'))->pluck('id');
        $this->assertTrue($ids->contains($daftar->id), 'Kunjungan yang sudah Selesai tapi belum bayar harus tetap muncul di daftar tagihan belum bayar.');
    }

    public function test_can_record_payment_after_status_is_selesai_but_not_yet_lunas(): void
    {
        $user = $this->authorizedUser(['DPP', 'BL']);
        $daftar = $this->makeDaftarPasien($user, ['biaya_registrasi' => 50000]);
        $this->actingAs($user)->post("/pemeriksaan/pasien/{$this->encode($daftar->id)}/updateStatus", ['f' => 1]);
        $this->assertDatabaseHas('t_daftarpasien', ['id' => $daftar->id, 'status' => 'Selesai']);

        $response = $this->actingAs($user)->post('/billing/process-paid', [
            'fk_daftarpasien' => $daftar->id,
            'total_tagihan' => 50000,
            'payment' => [['metode' => 1, 'nominal' => 50000]],
            'selisih' => 0,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('t_transactions', ['daftarpasien_id' => $daftar->id, 'amount' => 50000]);
    }

    public function test_finishing_examination_auto_discharges_an_active_rawat_inap_admission(): void
    {
        $user = $this->authorizedUser(['DPP']);
        $daftar = $this->makeDaftarPasien($user);
        $kamar = Kamar::create(['klienfk' => $user->klienfk, 'nama' => 'Kamar Auto Pulang', 'tarif_per_hari' => 100000, 'status' => 'kosong']);
        $admisi = app(RawatInapHelper::class)->admit(['klienfk' => $user->klienfk, 'fk_daftarpasien' => $daftar->id, 'fk_kamar' => $kamar->id]);

        $response = $this->actingAs($user)->post("/pemeriksaan/pasien/{$this->encode($daftar->id)}/updateStatus", [
            'f' => 1,
        ]);

        $response->assertOk();
        $response->assertJson(['admisi_dipulangkan' => true]);
        $this->assertDatabaseHas('t_rawatinap', ['id' => $admisi->id, 'status' => 'keluar']);
        $this->assertSame('kosong', $kamar->refresh()->status);
    }

    public function test_finishing_examination_without_an_active_admission_does_not_report_discharge(): void
    {
        $user = $this->authorizedUser(['DPP']);
        $daftar = $this->makeDaftarPasien($user);

        $response = $this->actingAs($user)->post("/pemeriksaan/pasien/{$this->encode($daftar->id)}/updateStatus", [
            'f' => 1,
        ]);

        $response->assertOk();
        $response->assertJson(['admisi_dipulangkan' => false]);
    }

    public function test_cannot_record_payment_when_already_lunas(): void
    {
        $user = $this->authorizedUser(['DPP', 'BL']);
        $daftar = $this->makeDaftarPasien($user, ['status' => 'Selesai', 'statuspembayaran' => 'Lunas']);

        $response = $this->actingAs($user)->post('/billing/process-paid', [
            'fk_daftarpasien' => $daftar->id,
            'total_tagihan' => 50000,
            'payment' => [['metode' => 1, 'nominal' => 50000]],
            'selisih' => 0,
        ]);

        $response->assertStatus(500);
        $this->assertStringContainsString('sudah lunas', $response->json('message'));
    }
}
