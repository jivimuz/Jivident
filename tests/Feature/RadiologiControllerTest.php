<?php

namespace Tests\Feature;

use App\Models\DaftarPasien;
use App\Models\JenisPemeriksaanRadiologi;
use App\Models\Module;
use App\Models\Patient;
use App\Models\RadiologiPasien;
use App\Models\Serial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RadiologiControllerTest extends TestCase
{
    use RefreshDatabase;

    private function authorizedUser(string $moduleCode): User
    {
        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addMonth()->toDateString(),
            'last_check' => now(),
        ]);

        $user = User::first();
        $user->access = (string) Module::where('code', $moduleCode)->value('id');
        $user->save();

        return $user;
    }

    private function makeDaftarPasien(User $user, Patient $patient): DaftarPasien
    {
        return DaftarPasien::create([
            'klienfk' => $user->klienfk,
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
    }

    public function test_radiologi_page_requires_access(): void
    {
        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addMonth()->toDateString(),
            'last_check' => now(),
        ]);
        $user = User::first();
        $user->access = '999999';
        $user->save();

        $response = $this->actingAs($user)->get('/radiologi');

        $response->assertRedirect(route('error'));
    }

    public function test_doctor_can_request_radiologi_with_clinical_notes(): void
    {
        $user = $this->authorizedUser('DPP');
        $patient = Patient::create(['klienfk' => $user->klienfk, 'norm' => 'RM070', 'nik' => '3030', 'nama' => 'Pasien Rad']);
        $daftar = $this->makeDaftarPasien($user, $patient);
        $jenis = JenisPemeriksaanRadiologi::create(['klienfk' => $user->klienfk, 'nama' => 'Panoramic', 'harga' => 150000]);
        $encodedId = base64_encode(base64_encode((string) $daftar->id));

        $response = $this->actingAs($user)->post("/pemeriksaan/pasien/{$encodedId}/radiologi/save", [
            'fk_jenispemeriksaan' => $jenis->id,
            'catatan_klinis' => 'Curiga impaksi gigi bungsu',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('t_radiologipasien', [
            'fk_daftarpasien' => $daftar->id,
            'jenis_nama' => 'Panoramic',
            'catatan_klinis' => 'Curiga impaksi gigi bungsu',
            'status' => 'diminta',
        ]);
    }

    public function test_request_requires_clinical_notes(): void
    {
        $user = $this->authorizedUser('DPP');
        $patient = Patient::create(['klienfk' => $user->klienfk, 'norm' => 'RM071', 'nik' => '3031', 'nama' => 'Pasien Tanpa Catatan']);
        $daftar = $this->makeDaftarPasien($user, $patient);
        $jenis = JenisPemeriksaanRadiologi::create(['klienfk' => $user->klienfk, 'nama' => 'Periapikal', 'harga' => 75000]);
        $encodedId = base64_encode(base64_encode((string) $daftar->id));

        $response = $this->actingAs($user)->postJson("/pemeriksaan/pasien/{$encodedId}/radiologi/save", [
            'fk_jenispemeriksaan' => $jenis->id,
        ]);

        $response->assertStatus(422);
    }

    public function test_full_workflow_diminta_to_terjadwal_to_selesai(): void
    {
        Storage::fake('public');
        $user = $this->authorizedUser('RAD');
        $patient = Patient::create(['klienfk' => $user->klienfk, 'norm' => 'RM072', 'nik' => '3032', 'nama' => 'Pasien Workflow']);
        $daftar = $this->makeDaftarPasien($user, $patient);
        $order = RadiologiPasien::create([
            'klienfk' => $user->klienfk,
            'fk_daftarpasien' => $daftar->id,
            'fk_pasien' => $patient->id,
            'jenis_nama' => 'Panoramic',
            'catatan_klinis' => 'Cek gigi bungsu',
            'status' => 'diminta',
            'tgl_diminta' => now(),
        ]);

        $jadwalResponse = $this->actingAs($user)->post("/radiologi/jadwalkan/{$order->id}", [
            'tgl_jadwal' => now()->addDay()->toDateTimeString(),
        ]);
        $jadwalResponse->assertOk();
        $this->assertDatabaseHas('t_radiologipasien', ['id' => $order->id, 'status' => 'terjadwal']);

        $gambar = UploadedFile::fake()->image('hasil.jpg');
        $hasilResponse = $this->actingAs($user)->post("/radiologi/saveHasil/{$order->id}", [
            'hasil_interpretasi' => 'Tidak ditemukan kelainan signifikan.',
            'gambar' => $gambar,
        ]);

        $hasilResponse->assertOk();
        $order->refresh();
        $this->assertSame('selesai', $order->status);
        $this->assertNotNull($order->gambar_path);
        Storage::disk('public')->assertExists($order->gambar_path);
    }

    public function test_cannot_jadwalkan_an_order_that_is_already_scheduled(): void
    {
        $user = $this->authorizedUser('RAD');
        $patient = Patient::create(['klienfk' => $user->klienfk, 'norm' => 'RM073', 'nik' => '3033', 'nama' => 'Pasien Sudah Jadwal']);
        $daftar = $this->makeDaftarPasien($user, $patient);
        $order = RadiologiPasien::create([
            'klienfk' => $user->klienfk,
            'fk_daftarpasien' => $daftar->id,
            'fk_pasien' => $patient->id,
            'jenis_nama' => 'Panoramic',
            'status' => 'terjadwal',
            'tgl_diminta' => now(),
            'tgl_jadwal' => now()->addDay(),
        ]);

        $response = $this->actingAs($user)->post("/radiologi/jadwalkan/{$order->id}", [
            'tgl_jadwal' => now()->addDays(2)->toDateTimeString(),
        ]);

        $response->assertStatus(404);
    }

    public function test_print_radiologi_shows_interpretation_and_image(): void
    {
        Storage::fake('public');
        $user = $this->authorizedUser('RAD');
        $patient = Patient::create(['klienfk' => $user->klienfk, 'norm' => 'RM074', 'nik' => '3034', 'nama' => 'Pasien Cetak Rad']);
        $daftar = $this->makeDaftarPasien($user, $patient);
        $order = RadiologiPasien::create([
            'klienfk' => $user->klienfk,
            'fk_daftarpasien' => $daftar->id,
            'fk_pasien' => $patient->id,
            'jenis_nama' => 'Panoramic',
            'status' => 'selesai',
            'tgl_diminta' => now(),
            'tgl_hasil' => now(),
            'hasil_interpretasi' => 'Tampak gigi impaksi pada regio 38.',
            'gambar_path' => 'radiologi/hasil/contoh.jpg',
        ]);
        $encodedId = base64_encode(base64_encode((string) $order->id));

        $response = $this->actingAs($user)->get("/print/print-radiologi/{$encodedId}");

        $response->assertOk();
        $response->assertSee('Tampak gigi impaksi pada regio 38.');
        $response->assertSee('Pasien Cetak Rad');
    }
}
