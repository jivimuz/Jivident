<?php

namespace Tests\Unit;

use App\Helpers\RawatInapHelper;
use App\Models\DaftarPasien;
use App\Models\DiagnosaPasien;
use App\Models\Kamar;
use App\Models\LabPasien;
use App\Models\Patient;
use App\Models\RadiologiPasien;
use App\Models\RawatInap;
use App\Models\ResepPasien;
use App\Models\TindakanPasien;
use App\Models\Transactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class RawatInapHelperTest extends TestCase
{
    use RefreshDatabase;

    private RawatInapHelper $helper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->helper = new RawatInapHelper;
    }

    private function makeDaftarPasien(array $overrides = []): DaftarPasien
    {
        $patient = Patient::create([
            'klienfk' => 1, 'norm' => 'RM-'.uniqid(), 'nik' => (string) random_int(1000000000, 9999999999), 'nama' => 'Pasien RWI',
        ]);

        return DaftarPasien::create(array_merge([
            'klienfk' => 1,
            'noregistrasi' => 'REG-'.uniqid(),
            'ruanganfk' => 1,
            'pasienfk' => $patient->id,
            'tglregistrasi' => now(),
            'statuspasien' => 'LAMA',
            'status' => 'Di Periksa',
            'noantrian' => 'A1',
            'statuspembayaran' => 'Belum Verifikasi',
            'biaya_registrasi' => 20000,
        ], $overrides));
    }

    private function makeKamar(float $tarif = 100000): Kamar
    {
        return Kamar::create(['klienfk' => 1, 'nama' => 'Kamar Test', 'tarif_per_hari' => $tarif, 'status' => 'kosong']);
    }

    public function test_admit_creates_admission_and_marks_room_as_terisi(): void
    {
        $daftar = $this->makeDaftarPasien();
        $kamar = $this->makeKamar();

        $admission = $this->helper->admit(['fk_daftarpasien' => $daftar->id, 'fk_kamar' => $kamar->id]);

        $this->assertSame('dirawat', $admission->status);
        $this->assertSame('terisi', $kamar->refresh()->status);
    }

    public function test_admit_fails_when_room_is_already_occupied(): void
    {
        $daftar = $this->makeDaftarPasien();
        $kamar = $this->makeKamar();
        $kamar->update(['status' => 'terisi']);

        $this->expectException(InvalidArgumentException::class);
        $this->helper->admit(['fk_daftarpasien' => $daftar->id, 'fk_kamar' => $kamar->id]);
    }

    public function test_admit_fails_when_visit_already_has_an_active_admission(): void
    {
        $daftar = $this->makeDaftarPasien();
        $kamar1 = $this->makeKamar();
        $kamar2 = $this->makeKamar();
        $this->helper->admit(['fk_daftarpasien' => $daftar->id, 'fk_kamar' => $kamar1->id]);

        $this->expectException(InvalidArgumentException::class);
        $this->helper->admit(['fk_daftarpasien' => $daftar->id, 'fk_kamar' => $kamar2->id]);
    }

    public function test_discharge_frees_the_room_and_marks_status_keluar(): void
    {
        $daftar = $this->makeDaftarPasien();
        $kamar = $this->makeKamar();
        $admission = $this->helper->admit(['fk_daftarpasien' => $daftar->id, 'fk_kamar' => $kamar->id]);

        $discharged = $this->helper->discharge($admission->id, ['ringkasan_pulang' => 'Kondisi stabil']);

        $this->assertSame('keluar', $discharged->status);
        $this->assertSame('Kondisi stabil', $discharged->ringkasan_pulang);
        $this->assertSame('kosong', $kamar->refresh()->status);
    }

    public function test_discharge_fails_if_already_discharged(): void
    {
        $daftar = $this->makeDaftarPasien();
        $kamar = $this->makeKamar();
        $admission = $this->helper->admit(['fk_daftarpasien' => $daftar->id, 'fk_kamar' => $kamar->id]);
        $this->helper->discharge($admission->id);

        $this->expectException(InvalidArgumentException::class);
        $this->helper->discharge($admission->id);
    }

    public function test_tambah_catatan_fails_for_a_discharged_admission(): void
    {
        $daftar = $this->makeDaftarPasien();
        $kamar = $this->makeKamar();
        $admission = $this->helper->admit(['fk_daftarpasien' => $daftar->id, 'fk_kamar' => $kamar->id]);
        $this->helper->discharge($admission->id);

        $this->expectException(InvalidArgumentException::class);
        $this->helper->tambahCatatan(['fk_rawatinap' => $admission->id, 'catatan' => 'Harus gagal']);
    }

    public function test_calculate_billing_charges_minimum_one_day_for_a_same_day_stay(): void
    {
        $daftar = $this->makeDaftarPasien(['biaya_registrasi' => 20000]);
        $kamar = $this->makeKamar(150000);
        $admission = $this->helper->admit(['fk_daftarpasien' => $daftar->id, 'fk_kamar' => $kamar->id]);

        $billing = $this->helper->calculateBilling($admission->id);

        $this->assertSame(1, $billing['jumlah_hari']);
        $this->assertEquals(150000, $billing['biaya_kamar']);
        $this->assertEquals(20000, $billing['biaya_registrasi']);
        $this->assertEquals(170000, $billing['total_tagihan']);
        $this->assertEquals(170000, $billing['sisa']);
    }

    public function test_calculate_billing_accumulates_room_fee_per_day_for_a_multi_day_stay(): void
    {
        $daftar = $this->makeDaftarPasien(['biaya_registrasi' => 0]);
        $kamar = $this->makeKamar(100000);
        $admission = RawatInap::create([
            'klienfk' => 1,
            'fk_daftarpasien' => $daftar->id,
            'fk_pasien' => $daftar->pasienfk,
            'fk_kamar' => $kamar->id,
            'tgl_masuk' => now()->subDays(2),
            'status' => 'dirawat',
        ]);
        $kamar->update(['status' => 'terisi']);

        $billing = $this->helper->calculateBilling($admission->id);

        // Masuk 2 hari lalu, belum keluar (dihitung sampai sekarang) = 3 hari.
        $this->assertSame(3, $billing['jumlah_hari']);
        $this->assertEquals(300000, $billing['biaya_kamar']);
    }

    public function test_calculate_billing_accumulates_tindakan_diagnosa_lab_radiologi_resep_and_subtracts_payments(): void
    {
        $daftar = $this->makeDaftarPasien(['biaya_registrasi' => 0]);
        $kamar = $this->makeKamar(0);
        $admission = $this->helper->admit(['fk_daftarpasien' => $daftar->id, 'fk_kamar' => $kamar->id]);

        TindakanPasien::create([
            'klienfk' => 1, 'fk_daftarpasien' => $daftar->id, 'tindakan' => 'Infus', 'dokter' => 'drg. A', 'qty' => 1, 'amount' => 50000,
        ]);
        DiagnosaPasien::create([
            'klienfk' => 1, 'fk_daftarpasien' => $daftar->id, 'fk_pasien' => $daftar->pasienfk, 'tipe_diagnosa' => 'X',
            'fk_diagnosa' => 1, 'diagnosa' => 'Demam', 'dokter' => 'drg. A', 'qty' => 1, 'amount' => 30000,
        ]);
        LabPasien::create([
            'klienfk' => 1, 'fk_daftarpasien' => $daftar->id, 'fk_pasien' => $daftar->pasienfk,
            'jenis_nama' => 'Darah Lengkap', 'harga_snapshot' => 40000, 'status' => 'selesai', 'tgl_diminta' => now(),
        ]);
        RadiologiPasien::create([
            'klienfk' => 1, 'fk_daftarpasien' => $daftar->id, 'fk_pasien' => $daftar->pasienfk,
            'jenis_nama' => 'Rontgen Dada', 'harga_snapshot' => 60000, 'status' => 'selesai', 'tgl_diminta' => now(),
        ]);
        ResepPasien::create([
            'klienfk' => 1, 'pasien_id' => $daftar->pasienfk, 'daftarpasien_id' => $daftar->id, 'type' => 'non-racik',
            'r_num' => 1, 'obat_name' => 'Paracetamol', 'qty' => 10, 'penggunaan_id' => 1, 'subtotal' => 15000,
        ]);
        Transactions::create([
            'klienfk' => 1, 'no_transaction' => 'TRX-RWI-1', 'daftarpasien_id' => $daftar->id,
            'transaction_type' => 'in', 'typecode' => 'BIL', 'amount' => 75000,
        ]);

        $billing = $this->helper->calculateBilling($admission->id);

        $this->assertEquals(50000, $billing['biaya_tindakan']);
        $this->assertEquals(30000, $billing['biaya_diagnosa']);
        $this->assertEquals(40000, $billing['biaya_lab']);
        $this->assertEquals(60000, $billing['biaya_radiologi']);
        $this->assertEquals(15000, $billing['biaya_resep']);
        $this->assertEquals(195000, $billing['total_tagihan']); // 0 kamar + 50k+30k+40k+60k+15k
        $this->assertEquals(75000, $billing['total_paid']);
        $this->assertEquals(120000, $billing['sisa']);
    }
}
