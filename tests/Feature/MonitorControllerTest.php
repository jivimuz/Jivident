<?php

namespace Tests\Feature;

use App\Helpers\RawatInapHelper;
use App\Models\DaftarPasien;
use App\Models\JadwalOperasi;
use App\Models\Kamar;
use App\Models\Patient;
use App\Models\RuanganInap;
use App\Models\RuanganOperasi;
use App\Models\Serial;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonitorControllerTest extends TestCase
{
    use RefreshDatabase;

    private function activateLicense(): void
    {
        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addMonth()->toDateString(),
            'last_check' => now(),
        ]);
    }

    private function makeDiperiksaVisit(int $klienfk, string $noAntrian): DaftarPasien
    {
        $patient = Patient::create([
            'klienfk' => $klienfk,
            'norm' => 'RM-'.uniqid(),
            'nik' => (string) random_int(1000000000, 9999999999),
            'nama' => 'Pasien Monitor',
        ]);

        return DaftarPasien::create([
            'klienfk' => $klienfk,
            'noregistrasi' => 'REG-'.uniqid(),
            'ruanganfk' => 1,
            'pasienfk' => $patient->id,
            'tglregistrasi' => now(),
            'tgldiperiksa' => now(),
            'statuspasien' => 'LAMA',
            'status' => 'Di Periksa',
            'noantrian' => $noAntrian,
            'statuspembayaran' => 'Belum Verifikasi',
            'biaya_registrasi' => 20000,
        ]);
    }

    public function test_current_antrian_only_shows_the_requested_branch(): void
    {
        $this->activateLicense();
        $this->makeDiperiksaVisit(1, 'A-CABANG-1');
        $this->makeDiperiksaVisit(2, 'B-CABANG-2');

        $responseBranch1 = $this->get('/monitor/currentAntrian?klienfk=1');
        $responseBranch2 = $this->get('/monitor/currentAntrian?klienfk=2');

        $responseBranch1->assertOk();
        $responseBranch1->assertJsonPath('current.nomor', 'A-CABANG-1');

        $responseBranch2->assertOk();
        $responseBranch2->assertJsonPath('current.nomor', 'B-CABANG-2');
    }

    public function test_current_antrian_defaults_to_branch_1_when_klienfk_is_not_given(): void
    {
        $this->activateLicense();
        $this->makeDiperiksaVisit(1, 'A-DEFAULT');
        $this->makeDiperiksaVisit(2, 'B-LAIN');

        $response = $this->get('/monitor/currentAntrian');

        $response->assertOk();
        $response->assertJsonPath('current.nomor', 'A-DEFAULT');
    }

    public function test_current_antrian_returns_empty_when_no_visit_in_that_branch_is_being_examined(): void
    {
        $this->activateLicense();
        $this->makeDiperiksaVisit(2, 'B-SAJA');

        $response = $this->get('/monitor/currentAntrian?klienfk=1');

        $response->assertOk();
        $response->assertJsonPath('status', 'empty');
    }

    public function test_monitor_ruangan_page_loads_without_login(): void
    {
        $this->activateLicense();

        $response = $this->get('/monitor/ruangan');

        $response->assertOk();
        $response->assertSee('Monitor Ruangan');
    }

    private function makeOccupiedBed(int $klienfk, string $pasienNama, string $norm): void
    {
        $ruangan = RuanganInap::create([
            'klienfk' => $klienfk,
            'nama' => 'Ruang Monitor',
            'lantai' => '1',
            'kelas' => 'Reguler',
            'tarif_per_hari' => 100000,
        ]);
        $kamar = Kamar::create([
            'klienfk' => $klienfk,
            'fk_ruangan' => $ruangan->id,
            'nomor_ranjang' => 1,
            'nama' => 'Ruang Monitor - Bed 1',
            'kelas' => 'Reguler',
            'tarif_per_hari' => 100000,
            'status' => 'kosong',
        ]);

        $patient = Patient::create([
            'klienfk' => $klienfk,
            'norm' => $norm,
            'nik' => (string) random_int(1000000000, 9999999999),
            'nama' => $pasienNama,
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

        app(RawatInapHelper::class)->admit([
            'klienfk' => $klienfk,
            'fk_daftarpasien' => $daftar->id,
            'fk_kamar' => $kamar->id,
        ]);
    }

    public function test_room_data_shows_occupied_bed_with_patient_name_and_norm(): void
    {
        $this->activateLicense();
        $this->makeOccupiedBed(1, 'Pasien Monitor Ruangan', 'RM-MONITOR-1');

        $response = $this->get('/monitor/roomData?klienfk=1');

        $response->assertOk();
        $response->assertJsonPath('status', 'ok');
        $response->assertJsonFragment(['pasien_nama' => 'Pasien Monitor Ruangan', 'pasien_norm' => 'RM-MONITOR-1']);
    }

    public function test_room_data_only_shows_the_requested_branch(): void
    {
        $this->activateLicense();
        $this->makeOccupiedBed(1, 'Pasien Cabang Satu', 'RM-1');
        $this->makeOccupiedBed(2, 'Pasien Cabang Dua', 'RM-2');

        $response = $this->get('/monitor/roomData?klienfk=1');

        $response->assertOk();
        $response->assertJsonFragment(['pasien_nama' => 'Pasien Cabang Satu']);
        $response->assertJsonMissing(['pasien_nama' => 'Pasien Cabang Dua']);
    }

    public function test_operasi_page_renders_without_login(): void
    {
        $this->activateLicense();

        $response = $this->get('/monitor/operasi?klienfk=1');

        $response->assertOk();
        $response->assertSee('Monitor Operasi');
    }

    public function test_operasi_data_only_shows_the_requested_branch(): void
    {
        $this->activateLicense();

        $ruangan1 = RuanganOperasi::create(['klienfk' => 1, 'nama' => 'OK 1', 'status' => 'tersedia']);
        $patient1 = Patient::create(['klienfk' => 1, 'norm' => 'RM-OP1', 'nik' => '1', 'nama' => 'Pasien Operasi Satu']);
        $dokter1 = User::first();
        JadwalOperasi::create([
            'klienfk' => 1, 'fk_ruangan_operasi' => $ruangan1->id, 'fk_pasien' => $patient1->id,
            'fk_dokter' => $dokter1->id, 'nama_operasi' => 'Operasi Cabang Satu',
            'tanggal' => '2026-06-10', 'jam_mulai' => '09:00', 'jam_selesai' => '10:00', 'status' => 'dijadwalkan',
        ]);

        $ruangan2 = RuanganOperasi::create(['klienfk' => 2, 'nama' => 'OK 2', 'status' => 'tersedia']);
        $patient2 = Patient::create(['klienfk' => 2, 'norm' => 'RM-OP2', 'nik' => '2', 'nama' => 'Pasien Operasi Dua']);
        JadwalOperasi::create([
            'klienfk' => 2, 'fk_ruangan_operasi' => $ruangan2->id, 'fk_pasien' => $patient2->id,
            'fk_dokter' => $dokter1->id, 'nama_operasi' => 'Operasi Cabang Dua',
            'tanggal' => '2026-06-10', 'jam_mulai' => '09:00', 'jam_selesai' => '10:00', 'status' => 'dijadwalkan',
        ]);

        $response = $this->get('/monitor/operasiData?klienfk=1&start=2026-06-01&end=2026-06-30');

        $response->assertOk();
        $titles = collect($response->json())->pluck('title')->implode(' | ');
        $this->assertStringContainsString('Pasien Operasi Satu', $titles);
        $this->assertStringNotContainsString('Pasien Operasi Dua', $titles);
    }

    public function test_operasi_status_reports_the_in_progress_case_per_room(): void
    {
        $this->activateLicense();
        Carbon::setTestNow('2026-06-10 10:00:00');

        $ruangan = RuanganOperasi::create(['klienfk' => 1, 'nama' => 'OK Status', 'status' => 'tersedia']);
        $patient = Patient::create(['klienfk' => 1, 'norm' => 'RM-OPS1', 'nik' => '9', 'nama' => 'Pasien Sedang Operasi']);
        JadwalOperasi::create([
            'klienfk' => 1, 'fk_ruangan_operasi' => $ruangan->id, 'fk_pasien' => $patient->id,
            'fk_dokter' => User::first()->id, 'nama_operasi' => 'Operasi Berjalan',
            'tanggal' => '2026-06-10', 'jam_mulai' => '09:00', 'jam_selesai' => '11:00', 'status' => 'berlangsung',
        ]);

        $response = $this->get('/monitor/operasiStatus?klienfk=1');

        $response->assertOk();
        $row = collect($response->json('data'))->firstWhere('id', $ruangan->id);
        $this->assertSame('Pasien Sedang Operasi', $row['current']['pasien']);
        $this->assertNull($row['next']);

        Carbon::setTestNow();
    }
}
