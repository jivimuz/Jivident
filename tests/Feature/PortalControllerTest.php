<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\DaftarPasien;
use App\Models\Employee;
use App\Models\JadwalDokter;
use App\Models\LabPasien;
use App\Models\Patient;
use App\Models\RadiologiPasien;
use App\Models\ResepPasien;
use App\Models\RuangPraktek;
use App\Models\Serial;
use App\Models\TindakanPasien;
use App\Models\Transactions;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalControllerTest extends TestCase
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

    private function makePatient(array $overrides = []): Patient
    {
        return Patient::create(array_merge([
            'klienfk' => 1,
            'norm' => 'RM-'.uniqid(),
            'nik' => (string) random_int(1000000000, 9999999999),
            'nama' => 'Pasien Portal',
            'tgllahir' => '1995-05-17',
        ], $overrides));
    }

    private function makeDaftarPasien(Patient $patient, array $overrides = []): DaftarPasien
    {
        return DaftarPasien::create(array_merge([
            'klienfk' => 1,
            'noregistrasi' => 'REG-'.uniqid(),
            'ruanganfk' => 1,
            'pasienfk' => $patient->id,
            'tglregistrasi' => now(),
            'statuspasien' => 'LAMA',
            'status' => 'Selesai',
            'noantrian' => 'A1',
            'statuspembayaran' => 'Belum Verifikasi',
            'biaya_registrasi' => 20000,
        ], $overrides));
    }

    private function makeDokterWithTemplate(): array
    {
        $employee = Employee::create(['fullname' => 'drg. Portal', 'nik' => 'NIK-'.uniqid(), 'is_pemeriksapasien' => true]);
        $dokter = User::forceCreate([
            'klienfk' => 1,
            'fk_employee' => $employee->id,
            'username' => 'drg_'.uniqid(),
            'display_name' => 'drg. Portal',
            'email' => uniqid().'@test.com',
            'password' => bcrypt('password'),
            'access' => '1',
        ]);
        $ruangPraktek = RuangPraktek::create(['klienfk' => 1, 'name' => 'Ruang Portal', 'code' => 'RP', 'fk_dokter' => $dokter->id]);

        $nextMonday = Carbon::now()->next(Carbon::MONDAY);
        JadwalDokter::create([
            'klienfk' => 1,
            'fk_dokter' => $dokter->id,
            'fk_ruangpraktek' => $ruangPraktek->id,
            'hari' => $nextMonday->dayOfWeek,
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'durasi_slot' => 30,
            'is_active' => true,
        ]);

        return ['dokter' => $dokter, 'ruangPraktek' => $ruangPraktek, 'tanggal' => $nextMonday->toDateString()];
    }

    public function test_protected_portal_routes_redirect_to_login_when_not_authenticated(): void
    {
        $this->activateLicense();

        $response = $this->get('/portal/riwayat');

        $response->assertRedirect(route('portal.login'));
    }

    public function test_login_with_correct_norm_and_birthdate_succeeds(): void
    {
        $this->activateLicense();
        $patient = $this->makePatient();

        $response = $this->post('/portal/login', [
            'norm' => $patient->norm,
            'tanggal_lahir' => '1995-05-17',
        ]);

        $response->assertRedirect(route('portal.dashboard'));
        $this->assertEquals($patient->id, session('portal_patient_id'));
    }

    public function test_login_with_wrong_birthdate_fails(): void
    {
        $this->activateLicense();
        $patient = $this->makePatient();

        $response = $this->post('/portal/login', [
            'norm' => $patient->norm,
            'tanggal_lahir' => '2000-01-01',
        ]);

        $response->assertSessionHasErrors('norm');
        $this->assertNull(session('portal_patient_id'));
    }

    public function test_logout_clears_the_session(): void
    {
        $this->activateLicense();
        $patient = $this->makePatient();
        $this->withSession(['portal_patient_id' => $patient->id]);

        $response = $this->post('/portal/logout');

        $response->assertRedirect(route('portal.login'));
        $this->assertNull(session('portal_patient_id'));
    }

    public function test_riwayat_only_shows_visits_belonging_to_the_logged_in_patient(): void
    {
        $this->activateLicense();
        $patientA = $this->makePatient(['nama' => 'Pasien A']);
        $patientB = $this->makePatient(['nama' => 'Pasien B']);
        $this->makeDaftarPasien($patientA, ['noregistrasi' => 'REG-A']);
        $this->makeDaftarPasien($patientB, ['noregistrasi' => 'REG-B']);

        $response = $this->withSession(['portal_patient_id' => $patientA->id])->get('/portal/riwayat');

        $response->assertOk();
        $response->assertSee('REG-A');
        $response->assertDontSee('REG-B');
    }

    public function test_tagihan_computes_outstanding_balance_correctly(): void
    {
        $this->activateLicense();
        $patient = $this->makePatient();
        $daftar = $this->makeDaftarPasien($patient, ['biaya_registrasi' => 20000]);

        TindakanPasien::create([
            'klienfk' => 1,
            'fk_daftarpasien' => $daftar->id,
            'tindakan' => 'Scaling',
            'dokter' => 'drg. A',
            'qty' => 1,
            'amount' => 50000,
        ]);
        Transactions::create([
            'klienfk' => 1,
            'no_transaction' => 'TRX-PORTAL-1',
            'daftarpasien_id' => $daftar->id,
            'transaction_type' => 'in',
            'typecode' => 'BIL',
            'amount' => 30000,
        ]);

        $response = $this->withSession(['portal_patient_id' => $patient->id])->get('/portal/tagihan');

        $response->assertOk();
        $response->assertSee('Rp 70.000');
        $response->assertSee('Rp 30.000');
        $response->assertSee('Rp 40.000');
    }

    public function test_tagihan_includes_lab_and_radiologi_charges(): void
    {
        $this->activateLicense();
        $patient = $this->makePatient();
        $daftar = $this->makeDaftarPasien($patient, ['biaya_registrasi' => 0]);

        LabPasien::create([
            'klienfk' => 1, 'fk_daftarpasien' => $daftar->id, 'fk_pasien' => $patient->id,
            'jenis_nama' => 'Gula Darah', 'harga_snapshot' => 25000, 'status' => 'selesai', 'tgl_diminta' => now(),
        ]);
        RadiologiPasien::create([
            'klienfk' => 1, 'fk_daftarpasien' => $daftar->id, 'fk_pasien' => $patient->id,
            'jenis_nama' => 'Panoramic', 'harga_snapshot' => 150000, 'status' => 'selesai', 'tgl_diminta' => now(),
        ]);

        $response = $this->withSession(['portal_patient_id' => $patient->id])->get('/portal/tagihan');

        $response->assertOk();
        $response->assertSee('Rp 175.000');
    }

    public function test_resep_lists_prescriptions_for_the_patient(): void
    {
        $this->activateLicense();
        $patient = $this->makePatient();
        $daftar = $this->makeDaftarPasien($patient);

        ResepPasien::create([
            'klienfk' => 1,
            'pasien_id' => $patient->id,
            'daftarpasien_id' => $daftar->id,
            'type' => 'non-racik',
            'r_num' => 1,
            'obat_name' => 'Amoxicillin',
            'qty' => 10,
            'unit' => 'tablet',
            'penggunaan_id' => 1,
            'signa' => '3x1 sehari',
            'subtotal' => 15000,
        ]);

        $response = $this->withSession(['portal_patient_id' => $patient->id])->get('/portal/resep');

        $response->assertOk();
        $response->assertSee('Amoxicillin');
        $response->assertSee('3x1 sehari');
    }

    public function test_patient_can_book_an_available_slot_for_themselves(): void
    {
        $this->activateLicense();
        $patient = $this->makePatient();
        $setup = $this->makeDokterWithTemplate();

        $response = $this->withSession(['portal_patient_id' => $patient->id])->post('/portal/booking/store', [
            'fk_dokter' => $setup['dokter']->id,
            'fk_ruangpraktek' => $setup['ruangPraktek']->id,
            'tanggal' => $setup['tanggal'],
            'jam_mulai' => '08:00',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('t_appointments', [
            'fk_pasien' => $patient->id,
            'fk_dokter' => $setup['dokter']->id,
            'status' => 'booked',
        ]);
    }

    public function test_patient_cannot_cancel_another_patients_appointment(): void
    {
        $this->activateLicense();
        $owner = $this->makePatient(['nama' => 'Pemilik Booking']);
        $intruder = $this->makePatient(['nama' => 'Bukan Pemilik']);
        $setup = $this->makeDokterWithTemplate();

        $appointment = Appointment::create([
            'klienfk' => 1,
            'fk_dokter' => $setup['dokter']->id,
            'fk_ruangpraktek' => $setup['ruangPraktek']->id,
            'fk_pasien' => $owner->id,
            'tanggal' => $setup['tanggal'],
            'jam_mulai' => '08:00',
            'jam_selesai' => '08:30',
            'status' => 'booked',
        ]);

        $response = $this->withSession(['portal_patient_id' => $intruder->id])
            ->post("/portal/booking/cancel/{$appointment->id}");

        $response->assertStatus(404);
        $this->assertDatabaseHas('t_appointments', ['id' => $appointment->id, 'status' => 'booked']);
    }
}
