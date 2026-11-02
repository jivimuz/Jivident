<?php

namespace Tests\Unit;

use App\Helpers\OperasiHelper;
use App\Models\Employee;
use App\Models\JadwalOperasi;
use App\Models\Patient;
use App\Models\RuanganOperasi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class OperasiHelperTest extends TestCase
{
    use RefreshDatabase;

    private OperasiHelper $helper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->helper = new OperasiHelper;
    }

    private function makeRuangan(array $overrides = []): RuanganOperasi
    {
        return RuanganOperasi::create(array_merge([
            'klienfk' => 1,
            'nama' => 'OK '.uniqid(),
            'status' => 'tersedia',
        ], $overrides));
    }

    private function makeDokter(string $name = 'drg. Bedah'): User
    {
        $employee = Employee::create(['fullname' => $name, 'nik' => 'NIK-'.uniqid(), 'is_pemeriksapasien' => true]);

        return User::forceCreate([
            'klienfk' => 1,
            'fk_employee' => $employee->id,
            'username' => 'dr_'.uniqid(),
            'display_name' => $name,
            'email' => uniqid().'@test.com',
            'password' => bcrypt('password'),
            'access' => '1',
        ]);
    }

    private function makePatient(array $overrides = []): Patient
    {
        return Patient::create(array_merge([
            'klienfk' => 1,
            'norm' => 'RM-'.uniqid(),
            'nik' => (string) random_int(1000000000, 9999999999),
            'nama' => 'Pasien Operasi',
        ], $overrides));
    }

    private function baseData(array $overrides = []): array
    {
        return array_merge([
            'fk_ruangan_operasi' => $this->makeRuangan()->id,
            'fk_pasien' => $this->makePatient()->id,
            'fk_dokter' => $this->makeDokter()->id,
            'nama_operasi' => 'Odontektomi',
            'tanggal' => now()->addDay()->toDateString(),
            'jam_mulai' => '09:00',
            'jam_selesai' => '10:00',
        ], $overrides);
    }

    public function test_create_schedules_successfully(): void
    {
        $jadwal = $this->helper->create($this->baseData());

        $this->assertSame('dijadwalkan', $jadwal->status);
        $this->assertDatabaseHas('t_jadwal_operasi', ['id' => $jadwal->id, 'klienfk' => 1]);
    }

    public function test_create_rejects_start_after_end(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->helper->create($this->baseData(['jam_mulai' => '11:00', 'jam_selesai' => '10:00']));
    }

    public function test_create_rejects_overlapping_room_booking(): void
    {
        $ruangan = $this->makeRuangan();
        $tanggal = now()->addDay()->toDateString();

        $this->helper->create($this->baseData([
            'fk_ruangan_operasi' => $ruangan->id,
            'tanggal' => $tanggal,
            'jam_mulai' => '09:00',
            'jam_selesai' => '11:00',
        ]));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Ruangan sudah dipakai');

        $this->helper->create($this->baseData([
            'fk_ruangan_operasi' => $ruangan->id,
            'tanggal' => $tanggal,
            'jam_mulai' => '10:00',
            'jam_selesai' => '12:00',
        ]));
    }

    public function test_create_allows_non_overlapping_times_in_same_room(): void
    {
        $ruangan = $this->makeRuangan();
        $tanggal = now()->addDay()->toDateString();

        $this->helper->create($this->baseData([
            'fk_ruangan_operasi' => $ruangan->id,
            'tanggal' => $tanggal,
            'jam_mulai' => '09:00',
            'jam_selesai' => '10:00',
        ]));

        $second = $this->helper->create($this->baseData([
            'fk_ruangan_operasi' => $ruangan->id,
            'tanggal' => $tanggal,
            'jam_mulai' => '10:00',
            'jam_selesai' => '11:00',
        ]));

        $this->assertNotNull($second->id);
    }

    public function test_create_rejects_overlapping_doctor_booking_in_a_different_room(): void
    {
        $dokter = $this->makeDokter();
        $tanggal = now()->addDay()->toDateString();

        $this->helper->create($this->baseData([
            'fk_dokter' => $dokter->id,
            'fk_ruangan_operasi' => $this->makeRuangan()->id,
            'tanggal' => $tanggal,
            'jam_mulai' => '09:00',
            'jam_selesai' => '11:00',
        ]));

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Dokter sudah dijadwalkan');

        $this->helper->create($this->baseData([
            'fk_dokter' => $dokter->id,
            'fk_ruangan_operasi' => $this->makeRuangan()->id,
            'tanggal' => $tanggal,
            'jam_mulai' => '10:00',
            'jam_selesai' => '12:00',
        ]));
    }

    public function test_cancelled_schedule_does_not_block_the_same_room_slot(): void
    {
        $ruangan = $this->makeRuangan();
        $tanggal = now()->addDay()->toDateString();

        $jadwal = $this->helper->create($this->baseData([
            'fk_ruangan_operasi' => $ruangan->id,
            'tanggal' => $tanggal,
            'jam_mulai' => '09:00',
            'jam_selesai' => '11:00',
        ]));
        $this->helper->cancel($jadwal);

        $second = $this->helper->create($this->baseData([
            'fk_ruangan_operasi' => $ruangan->id,
            'tanggal' => $tanggal,
            'jam_mulai' => '09:00',
            'jam_selesai' => '11:00',
        ]));

        $this->assertNotNull($second->id);
    }

    public function test_reschedule_rechecks_conflict_excluding_itself(): void
    {
        $ruangan = $this->makeRuangan();
        $tanggal = now()->addDay()->toDateString();

        $jadwal = $this->helper->create($this->baseData([
            'fk_ruangan_operasi' => $ruangan->id,
            'tanggal' => $tanggal,
            'jam_mulai' => '09:00',
            'jam_selesai' => '10:00',
        ]));

        // Reschedule ke jam lain, masih ruangan yang sama — tidak boleh
        // "bentrok dengan dirinya sendiri".
        $updated = $this->helper->reschedule($jadwal, ['jam_mulai' => '13:00', 'jam_selesai' => '14:00']);

        $this->assertSame('13:00', (string) $updated->jam_mulai);
    }

    public function test_update_status_transitions_and_rejects_invalid_value(): void
    {
        $jadwal = $this->helper->create($this->baseData());

        $this->helper->updateStatus($jadwal, 'berlangsung');
        $this->assertSame('berlangsung', $jadwal->fresh()->status);

        $this->helper->updateStatus($jadwal, 'selesai');
        $this->assertSame('selesai', $jadwal->fresh()->status);

        $this->expectException(InvalidArgumentException::class);
        $this->helper->updateStatus($jadwal, 'dibatalkan');
    }

    public function test_cancel_rejects_an_already_cancelled_schedule(): void
    {
        $jadwal = $this->helper->create($this->baseData());
        $this->helper->cancel($jadwal);

        $this->expectException(InvalidArgumentException::class);
        $this->helper->cancel($jadwal);
    }

    public function test_cancel_rejects_a_completed_schedule(): void
    {
        $jadwal = $this->helper->create($this->baseData());
        $this->helper->updateStatus($jadwal, 'berlangsung');
        $this->helper->updateStatus($jadwal, 'selesai');

        $this->expectException(InvalidArgumentException::class);
        $this->helper->cancel($jadwal);
    }

    public function test_get_calendar_events_only_returns_schedules_within_range_and_branch(): void
    {
        $this->helper->create($this->baseData(['tanggal' => '2026-06-10']));
        $otherRuangan = RuanganOperasi::create(['klienfk' => 2, 'nama' => 'OK Lain', 'status' => 'tersedia']);
        JadwalOperasi::create([
            'klienfk' => 2,
            'fk_ruangan_operasi' => $otherRuangan->id,
            'fk_pasien' => $this->makePatient()->id,
            'fk_dokter' => $this->makeDokter()->id,
            'nama_operasi' => 'Operasi Cabang Lain',
            'tanggal' => '2026-06-10',
            'jam_mulai' => '09:00',
            'jam_selesai' => '10:00',
            'status' => 'dijadwalkan',
        ]);

        $events = $this->helper->getCalendarEvents(1, '2026-06-01', '2026-06-30');

        $this->assertCount(1, $events);
        $this->assertStringNotContainsString('Cabang Lain', $events[0]['title']);
    }

    public function test_room_statuses_reports_an_in_progress_case_as_current(): void
    {
        Carbon::setTestNow('2026-06-10 10:00:00');

        $ruangan = $this->makeRuangan();
        $patient = $this->makePatient(['nama' => 'Pasien Sedang Operasi']);
        JadwalOperasi::create([
            'klienfk' => 1,
            'fk_ruangan_operasi' => $ruangan->id,
            'fk_pasien' => $patient->id,
            'fk_dokter' => $this->makeDokter()->id,
            'nama_operasi' => 'Operasi Berjalan',
            'tanggal' => '2026-06-10',
            'jam_mulai' => '09:00',
            'jam_selesai' => '11:00',
            'status' => 'berlangsung',
        ]);

        $statuses = $this->helper->getRoomStatuses(1);
        $row = collect($statuses)->firstWhere('id', $ruangan->id);

        $this->assertNotNull($row['current']);
        $this->assertSame('Pasien Sedang Operasi', $row['current']['pasien']);
        $this->assertNull($row['next']);

        Carbon::setTestNow();
    }

    public function test_room_statuses_reports_the_soonest_upcoming_case_as_next_and_ignores_past_ones(): void
    {
        Carbon::setTestNow('2026-06-10 08:00:00');

        $ruangan = $this->makeRuangan();
        $patientLewat = $this->makePatient(['nama' => 'Pasien Jam Lewat']);
        JadwalOperasi::create([
            'klienfk' => 1,
            'fk_ruangan_operasi' => $ruangan->id,
            'fk_pasien' => $patientLewat->id,
            'fk_dokter' => $this->makeDokter()->id,
            'nama_operasi' => 'Operasi Jam Lewat',
            'tanggal' => '2026-06-10',
            'jam_mulai' => '07:00',
            'jam_selesai' => '07:30',
            'status' => 'dijadwalkan',
        ]);
        $patientBerikutnya = $this->makePatient(['nama' => 'Pasien Berikutnya']);
        JadwalOperasi::create([
            'klienfk' => 1,
            'fk_ruangan_operasi' => $ruangan->id,
            'fk_pasien' => $patientBerikutnya->id,
            'fk_dokter' => $this->makeDokter()->id,
            'nama_operasi' => 'Operasi Berikutnya',
            'tanggal' => '2026-06-10',
            'jam_mulai' => '10:00',
            'jam_selesai' => '11:00',
            'status' => 'dijadwalkan',
        ]);

        $statuses = $this->helper->getRoomStatuses(1);
        $row = collect($statuses)->firstWhere('id', $ruangan->id);

        $this->assertNull($row['current']);
        $this->assertNotNull($row['next']);
        $this->assertSame('Pasien Berikutnya', $row['next']['pasien']);

        Carbon::setTestNow();
    }

    public function test_room_statuses_is_empty_for_a_room_with_no_schedule_today(): void
    {
        Carbon::setTestNow('2026-06-10 08:00:00');

        $ruangan = $this->makeRuangan();

        $statuses = $this->helper->getRoomStatuses(1);
        $row = collect($statuses)->firstWhere('id', $ruangan->id);

        $this->assertNull($row['current']);
        $this->assertNull($row['next']);

        Carbon::setTestNow();
    }

    public function test_room_statuses_is_scoped_to_the_given_branch(): void
    {
        RuanganOperasi::create(['klienfk' => 2, 'nama' => 'OK Cabang Lain', 'status' => 'tersedia']);

        $statuses = $this->helper->getRoomStatuses(1);

        $this->assertFalse(collect($statuses)->contains('nama', 'OK Cabang Lain'));
    }
}
