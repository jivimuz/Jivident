<?php

namespace Tests\Unit;

use App\Helpers\AppointmentHelper;
use App\Models\Employee;
use App\Models\JadwalDokter;
use App\Models\Patient;
use App\Models\RuangPraktek;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class AppointmentHelperTest extends TestCase
{
    use RefreshDatabase;

    private AppointmentHelper $helper;

    private User $dokter;

    private RuangPraktek $ruangPraktek;

    private Patient $patient;

    private string $nextMonday;

    private string $nextTuesday;

    protected function setUp(): void
    {
        parent::setUp();
        $this->helper = app(AppointmentHelper::class);

        $employee = Employee::create([
            'fullname' => 'drg. Test',
            'nik' => 'NIK-'.uniqid(),
            'is_pemeriksapasien' => true,
        ]);
        $this->dokter = User::forceCreate([
            'klienfk' => 1,
            'fk_employee' => $employee->id,
            'username' => 'drgtest_'.uniqid(),
            'display_name' => 'drg. Test',
            'email' => uniqid().'@test.com',
            'password' => bcrypt('password'),
            'access' => '1',
        ]);
        $this->ruangPraktek = RuangPraktek::create([
            'klienfk' => 1,
            'name' => 'Ruang 1',
            'code' => 'R1',
            'fk_dokter' => $this->dokter->id,
        ]);
        $this->patient = Patient::create([
            'klienfk' => 1,
            'norm' => 'RM001',
            'nik' => '1111',
            'nama' => 'Pasien Test',
        ]);

        $this->actingAs($this->dokter);

        $this->nextMonday = Carbon::now()->next(Carbon::MONDAY)->toDateString();
        $this->nextTuesday = Carbon::now()->next(Carbon::TUESDAY)->toDateString();
    }

    private function makeTemplate(int $hari, string $jamMulai, string $jamSelesai, int $durasi = 30): JadwalDokter
    {
        return JadwalDokter::create([
            'klienfk' => 1,
            'fk_dokter' => $this->dokter->id,
            'fk_ruangpraktek' => $this->ruangPraktek->id,
            'hari' => $hari,
            'jam_mulai' => $jamMulai,
            'jam_selesai' => $jamSelesai,
            'durasi_slot' => $durasi,
            'is_active' => true,
        ]);
    }

    public function test_generates_correct_number_of_slots_from_template(): void
    {
        // Senin (hari=1), 08:00-10:00, slot 30 menit -> 4 slot
        $this->makeTemplate(1, '08:00', '10:00', 30);

        // $this->nextMonday
        $slots = $this->helper->getAvailableSlots($this->dokter->id, $this->ruangPraktek->id, $this->nextMonday);

        $this->assertCount(4, $slots);
        $this->assertSame('08:00', $slots[0]['jam_mulai']);
        $this->assertSame('08:30', $slots[0]['jam_selesai']);
        $this->assertSame('09:30', $slots[3]['jam_mulai']);
        $this->assertTrue($slots[0]['available']);
    }

    public function test_returns_no_slots_on_a_day_without_template(): void
    {
        $this->makeTemplate(1, '08:00', '10:00', 30); // Senin saja

        // $this->nextTuesday (bukan hari Senin, jadi tidak match template)
        $slots = $this->helper->getAvailableSlots($this->dokter->id, $this->ruangPraktek->id, $this->nextTuesday);

        $this->assertEmpty($slots);
    }

    public function test_booking_marks_slot_as_unavailable_for_subsequent_lookups(): void
    {
        $this->makeTemplate(1, '08:00', '10:00', 30);

        $this->helper->book([
            'fk_dokter' => $this->dokter->id,
            'fk_ruangpraktek' => $this->ruangPraktek->id,
            'fk_pasien' => $this->patient->id,
            'tanggal' => $this->nextMonday,
            'jam_mulai' => '08:30',
        ]);

        $slots = $this->helper->getAvailableSlots($this->dokter->id, $this->ruangPraktek->id, $this->nextMonday);
        $bookedSlot = collect($slots)->firstWhere('jam_mulai', '08:30');

        $this->assertFalse($bookedSlot['available']);
    }

    public function test_cannot_double_book_the_same_slot(): void
    {
        $this->makeTemplate(1, '08:00', '10:00', 30);

        $this->helper->book([
            'fk_dokter' => $this->dokter->id,
            'fk_ruangpraktek' => $this->ruangPraktek->id,
            'fk_pasien' => $this->patient->id,
            'tanggal' => $this->nextMonday,
            'jam_mulai' => '08:30',
        ]);

        $this->expectException(InvalidArgumentException::class);

        $this->helper->book([
            'fk_dokter' => $this->dokter->id,
            'fk_ruangpraktek' => $this->ruangPraktek->id,
            'fk_pasien' => $this->patient->id,
            'tanggal' => $this->nextMonday,
            'jam_mulai' => '08:30',
        ]);
    }

    public function test_cannot_book_a_time_outside_the_template(): void
    {
        $this->makeTemplate(1, '08:00', '10:00', 30);

        $this->expectException(InvalidArgumentException::class);

        $this->helper->book([
            'fk_dokter' => $this->dokter->id,
            'fk_ruangpraktek' => $this->ruangPraktek->id,
            'fk_pasien' => $this->patient->id,
            'tanggal' => $this->nextMonday,
            'jam_mulai' => '15:00', // luar jam praktek
        ]);
    }

    public function test_cancelling_a_booking_frees_up_the_slot_again(): void
    {
        $this->makeTemplate(1, '08:00', '10:00', 30);

        $appointment = $this->helper->book([
            'fk_dokter' => $this->dokter->id,
            'fk_ruangpraktek' => $this->ruangPraktek->id,
            'fk_pasien' => $this->patient->id,
            'tanggal' => $this->nextMonday,
            'jam_mulai' => '08:30',
        ]);

        $this->helper->cancel($appointment->id);

        $slots = $this->helper->getAvailableSlots($this->dokter->id, $this->ruangPraktek->id, $this->nextMonday);
        $slot = collect($slots)->firstWhere('jam_mulai', '08:30');

        $this->assertTrue($slot['available']);
    }

    public function test_cannot_cancel_an_already_cancelled_booking(): void
    {
        $this->makeTemplate(1, '08:00', '10:00', 30);

        $appointment = $this->helper->book([
            'fk_dokter' => $this->dokter->id,
            'fk_ruangpraktek' => $this->ruangPraktek->id,
            'fk_pasien' => $this->patient->id,
            'tanggal' => $this->nextMonday,
            'jam_mulai' => '08:30',
        ]);
        $this->helper->cancel($appointment->id);

        $this->expectException(InvalidArgumentException::class);
        $this->helper->cancel($appointment->id);
    }
}
