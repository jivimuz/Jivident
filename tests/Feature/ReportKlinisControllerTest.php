<?php

namespace Tests\Feature;

use App\Models\Cuti;
use App\Models\DaftarPasien;
use App\Models\DiagnosaPasien;
use App\Models\JadwalKerjaKaryawan;
use App\Models\JenisCuti;
use App\Models\Module;
use App\Models\Patient;
use App\Models\PayrollSlip;
use App\Models\Serial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportKlinisControllerTest extends TestCase
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
        $user->access = (string) Module::where('code', 'RPC')->value('id');
        $user->save();

        return $user;
    }

    public function test_report_klinis_page_requires_access(): void
    {
        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addMonth()->toDateString(),
            'last_check' => now(),
        ]);
        $user = User::first();
        $user->access = '999999';
        $user->save();

        $response = $this->actingAs($user)->get('/report-klinis');

        $response->assertRedirect(route('error'));
    }

    public function test_trend_kunjungan_splits_new_vs_returning_patients(): void
    {
        $user = $this->authorizedUser();

        $patient = Patient::create([
            'klienfk' => $user->klienfk,
            'norm' => 'RM020',
            'nik' => '4444',
            'nama' => 'Pasien Trend',
        ]);

        DaftarPasien::create([
            'klienfk' => $user->klienfk,
            'noregistrasi' => 'REG-010',
            'ruanganfk' => 1,
            'pasienfk' => $patient->id,
            'tglregistrasi' => now(),
            'statuspasien' => 'BARU',
            'status' => 'Selesai',
            'noantrian' => 'A1',
            'statuspembayaran' => 'Lunas',
            'biaya_registrasi' => 20000,
        ]);
        DaftarPasien::create([
            'klienfk' => $user->klienfk,
            'noregistrasi' => 'REG-011',
            'ruanganfk' => 1,
            'pasienfk' => $patient->id,
            'tglregistrasi' => now(),
            'statuspasien' => 'LAMA',
            'status' => 'Selesai',
            'noantrian' => 'A2',
            'statuspembayaran' => 'Lunas',
            'biaya_registrasi' => 20000,
        ]);

        $response = $this->actingAs($user)->post('/report-klinis/getTrendKunjungan', [
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->toDateString(),
        ]);

        $response->assertOk();
        $today = collect($response->json('data'))->firstWhere('tanggal', now()->toDateString());
        $this->assertSame(1, $today['baru']);
        $this->assertSame(1, $today['lama']);
        $this->assertSame(2, $today['total']);
    }

    public function test_diagnosa_terbanyak_groups_by_diagnosa_name(): void
    {
        $user = $this->authorizedUser();

        foreach (range(1, 2) as $i) {
            DiagnosaPasien::create([
                'klienfk' => $user->klienfk,
                'fk_daftarpasien' => 1,
                'fk_pasien' => 1,
                'tipe_diagnosa' => 'X',
                'fk_diagnosa' => 1,
                'diagnosa' => 'Karies Gigi',
                'dokter' => 'drg. A',
                'qty' => 1,
            ]);
        }

        $response = $this->actingAs($user)->post('/report-klinis/getDiagnosaTerbanyak', [
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->toDateString(),
        ]);

        $response->assertOk();
        $data = $response->json('data');
        $this->assertSame('Karies Gigi', $data[0]['diagnosa']);
        $this->assertSame(2, $data[0]['jumlah']);
    }

    public function test_rekap_payroll_sums_company_cost_and_take_home_pay(): void
    {
        $user = $this->authorizedUser();
        $periode = now()->format('Y-m');

        PayrollSlip::create([
            'fk_employee' => 1,
            'periode' => $periode,
            'gaji_pokok' => 5000000,
            'bpjs_kesehatan_company' => 20000,
            'pph21_nominal' => 100000,
            'gaji_bersih' => 4880000,
        ]);

        $response = $this->actingAs($user)->post('/report-klinis/getRekapPayroll', [
            'periode' => $periode,
        ]);

        $response->assertOk();
        $this->assertEquals(4880000, $response->json('total_gaji_bersih'));
        $this->assertEquals(5020000, $response->json('total_beban_perusahaan'));
    }

    public function test_kehadiran_cuti_recaps_shift_hours_and_approved_leave_days(): void
    {
        $user = $this->authorizedUser();
        $jenisCuti = JenisCuti::first();

        JadwalKerjaKaryawan::create([
            'fk_employee' => 1,
            'tanggal' => now()->toDateString(),
            'jam_masuk' => now()->setTime(8, 0),
            'jam_pulang' => now()->setTime(16, 0),
        ]);

        Cuti::create([
            'kode' => 'CT-001',
            'fk_employee' => 1,
            'fk_jeniscuti' => $jenisCuti->id,
            'tglpermohonan' => now(),
            'nopermohonan' => 'NP-001',
            'tambah' => 0,
            'kurang' => 1,
            'saldo' => 11,
            'startdate' => now(),
            'enddate' => now(),
            'status' => 'approved',
        ]);

        $response = $this->actingAs($user)->post('/report-klinis/getKehadiranCuti', [
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->toDateString(),
        ]);

        $response->assertOk();
        $kehadiran = $response->json('kehadiran');
        $this->assertSame(1, $kehadiran[0]['jumlah_shift']);
        $this->assertEquals(8, $kehadiran[0]['total_jam']);

        $cuti = $response->json('cuti');
        $this->assertSame(1, $cuti[0]['jumlah_pengajuan']);
        $this->assertEquals(1, $cuti[0]['total_hari']);
    }
}
