<?php

namespace Tests\Feature;

use App\Models\DaftarPasien;
use App\Models\Patient;
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
}
