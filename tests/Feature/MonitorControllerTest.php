<?php

namespace Tests\Feature;

use App\Helpers\RawatInapHelper;
use App\Models\DaftarPasien;
use App\Models\Kamar;
use App\Models\Patient;
use App\Models\RuanganInap;
use App\Models\Serial;
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
}
