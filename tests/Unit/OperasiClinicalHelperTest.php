<?php

namespace Tests\Unit;

use App\Helpers\OperasiClinicalHelper;
use App\Models\Employee;
use App\Models\JadwalOperasi;
use App\Models\Patient;
use App\Models\RuanganOperasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class OperasiClinicalHelperTest extends TestCase
{
    use RefreshDatabase;

    private function schedule(): JadwalOperasi
    {
        $employee = Employee::create(['fullname' => 'dr. OK', 'nik' => 'NIK-'.uniqid(), 'is_pemeriksapasien' => true]);
        $doctor = User::forceCreate([
            'klienfk' => 1, 'fk_employee' => $employee->id, 'username' => 'ok_'.uniqid(),
            'display_name' => 'dr. OK', 'email' => uniqid().'@test.com', 'password' => bcrypt('password'), 'access' => '1',
        ]);
        $patient = Patient::create([
            'klienfk' => 1, 'norm' => 'RM-'.uniqid(), 'nik' => (string) random_int(1000000000, 9999999999), 'nama' => 'Pasien OK',
        ]);
        $room = RuanganOperasi::create(['klienfk' => 1, 'nama' => 'OK '.uniqid(), 'status' => 'tersedia']);

        return JadwalOperasi::create([
            'klienfk' => 1, 'fk_ruangan_operasi' => $room->id, 'fk_pasien' => $patient->id,
            'fk_dokter' => $doctor->id, 'nama_operasi' => 'Operasi', 'tanggal' => now()->addDay(),
            'jam_mulai' => '09:00', 'jam_selesai' => '10:00', 'status' => 'dijadwalkan',
        ]);
    }

    public function test_record_and_postoperative_monitoring_are_saved(): void
    {
        $jadwal = $this->schedule();
        $helper = new OperasiClinicalHelper;
        $record = $helper->saveRecord($jadwal, [
            'preop_checklist' => ['identity' => true, 'consent' => true],
            'anesthesia_plan' => 'Lokal',
            'procedure_notes' => 'Tindakan berjalan baik.',
            'consumables' => [['name' => 'Benang', 'qty' => 1]],
            'recovery_status' => 'stabil',
        ], 1);
        $monitoring = $helper->addMonitoring($record, [
            'pain_scale' => 2, 'systolic' => 120, 'diastolic' => 80, 'spo2' => 98,
        ], 1);

        $this->assertSame($record->id, $monitoring->operasi_record_id);
        $this->assertDatabaseHas('t_operasi_records', ['id' => $record->id]);
    }

    public function test_monitoring_rejects_out_of_range_values(): void
    {
        $record = (new OperasiClinicalHelper)->saveRecord($this->schedule(), [], 1);

        $this->expectException(InvalidArgumentException::class);
        (new OperasiClinicalHelper)->addMonitoring($record, ['pain_scale' => 11, 'spo2' => 98], 1);
    }

    public function test_cancelled_schedule_cannot_receive_a_record(): void
    {
        $jadwal = $this->schedule();
        $jadwal->update(['status' => 'dibatalkan']);

        $this->expectException(InvalidArgumentException::class);
        (new OperasiClinicalHelper)->saveRecord($jadwal, [], 1);
    }
}
