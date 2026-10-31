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
use App\Models\RuanganInap;
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

    /**
     * Regresi: discharge() sebelumnya tidak menyentuh t_daftarpasien sama
     * sekali, jadi kunjungan dasar rawat inap tidak pernah ditandai Selesai
     * (beda dengan alur rawat jalan biasa lewat PemeriksaanController).
     */
    public function test_discharge_marks_the_underlying_visit_as_selesai(): void
    {
        $daftar = $this->makeDaftarPasien();
        $kamar = $this->makeKamar();
        $admission = $this->helper->admit(['fk_daftarpasien' => $daftar->id, 'fk_kamar' => $kamar->id]);

        $this->helper->discharge($admission->id);

        $this->assertSame('Selesai', $daftar->refresh()->status);
    }

    public function test_pindah_ruangan_moves_admission_frees_old_room_and_occupies_new(): void
    {
        $daftar = $this->makeDaftarPasien();
        $kamarAsal = $this->makeKamar();
        $kamarTujuan = $this->makeKamar();
        $admission = $this->helper->admit(['fk_daftarpasien' => $daftar->id, 'fk_kamar' => $kamarAsal->id]);

        $pindah = $this->helper->pindahRuangan($admission->id, [
            'fk_kamar_tujuan' => $kamarTujuan->id,
            'catatan' => 'Minta pindah ke kamar lebih tenang',
        ]);

        $this->assertSame($kamarAsal->id, $pindah->fk_kamar_asal);
        $this->assertSame($kamarTujuan->id, $pindah->fk_kamar_tujuan);
        $this->assertSame($kamarTujuan->id, $admission->refresh()->fk_kamar);
        $this->assertSame('kosong', $kamarAsal->refresh()->status);
        $this->assertSame('terisi', $kamarTujuan->refresh()->status);
    }

    public function test_pindah_ruangan_rejects_when_target_room_is_occupied(): void
    {
        $daftar = $this->makeDaftarPasien();
        $kamarAsal = $this->makeKamar();
        $kamarTujuan = $this->makeKamar();
        $kamarTujuan->update(['status' => 'terisi']);
        $admission = $this->helper->admit(['fk_daftarpasien' => $daftar->id, 'fk_kamar' => $kamarAsal->id]);

        $this->expectException(InvalidArgumentException::class);
        $this->helper->pindahRuangan($admission->id, ['fk_kamar_tujuan' => $kamarTujuan->id]);
    }

    public function test_pindah_ruangan_rejects_moving_to_the_same_room(): void
    {
        $daftar = $this->makeDaftarPasien();
        $kamar = $this->makeKamar();
        $admission = $this->helper->admit(['fk_daftarpasien' => $daftar->id, 'fk_kamar' => $kamar->id]);

        $this->expectException(InvalidArgumentException::class);
        $this->helper->pindahRuangan($admission->id, ['fk_kamar_tujuan' => $kamar->id]);
    }

    public function test_pindah_ruangan_rejects_when_admission_already_discharged(): void
    {
        $daftar = $this->makeDaftarPasien();
        $kamarAsal = $this->makeKamar();
        $kamarTujuan = $this->makeKamar();
        $admission = $this->helper->admit(['fk_daftarpasien' => $daftar->id, 'fk_kamar' => $kamarAsal->id]);
        $this->helper->discharge($admission->id);

        $this->expectException(InvalidArgumentException::class);
        $this->helper->pindahRuangan($admission->id, ['fk_kamar_tujuan' => $kamarTujuan->id]);
    }

    public function test_calculate_billing_splits_room_cost_across_transfer_segments(): void
    {
        $daftar = $this->makeDaftarPasien(['biaya_registrasi' => 0]);
        $kamarReguler = $this->makeKamar(100000);
        $kamarVip = $this->makeKamar(300000);

        $admission = RawatInap::create([
            'klienfk' => 1,
            'fk_daftarpasien' => $daftar->id,
            'fk_pasien' => $daftar->pasienfk,
            'fk_kamar' => $kamarReguler->id,
            'tgl_masuk' => '2026-01-01 08:00:00',
            'status' => 'dirawat',
        ]);
        $kamarReguler->update(['status' => 'terisi']);

        // 3 hari di kamar reguler (1, 2, 3 Jan), lalu pindah ke VIP 4 Jan,
        // dipulangkan 5 Jan -> 2 hari di VIP (4, 5 Jan) termasuk hari pulang.
        $this->helper->pindahRuangan($admission->id, [
            'fk_kamar_tujuan' => $kamarVip->id,
            'waktu_pindah' => '2026-01-04 10:00:00',
        ]);
        $admission->update(['tgl_keluar' => '2026-01-05 14:00:00', 'status' => 'keluar']);

        $billing = $this->helper->calculateBilling($admission->id);

        $this->assertSame(5, $billing['jumlah_hari']);
        $this->assertEquals(900000, $billing['biaya_kamar']); // 3*100000 + 2*300000
        $this->assertCount(2, $billing['rincian_kamar']);
        $this->assertSame(3, $billing['rincian_kamar'][0]['hari']);
        $this->assertEquals(300000, $billing['rincian_kamar'][0]['subtotal']);
        $this->assertSame(2, $billing['rincian_kamar'][1]['hari']);
        $this->assertEquals(600000, $billing['rincian_kamar'][1]['subtotal']);
    }

    public function test_get_pindah_log_returns_history_newest_first(): void
    {
        $daftar = $this->makeDaftarPasien();
        $kamarA = $this->makeKamar();
        $kamarB = $this->makeKamar();
        $kamarC = $this->makeKamar();
        $admission = $this->helper->admit(['fk_daftarpasien' => $daftar->id, 'fk_kamar' => $kamarA->id]);

        $this->helper->pindahRuangan($admission->id, ['fk_kamar_tujuan' => $kamarB->id, 'waktu_pindah' => now()->subDay()]);
        $this->helper->pindahRuangan($admission->id, ['fk_kamar_tujuan' => $kamarC->id, 'waktu_pindah' => now()]);

        $log = $this->helper->getPindahLog($admission->id);

        $this->assertCount(2, $log);
        $this->assertSame($kamarB->id, $log->first()->fk_kamar_asal);
        $this->assertSame($kamarC->id, $log->first()->fk_kamar_tujuan);
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

    public function test_create_ruangan_generates_one_bed_row_per_jumlah_ranjang(): void
    {
        $ruangan = $this->helper->createRuangan([
            'nama' => 'Ruang Melati', 'kelas' => 'VIP', 'tarif_per_hari' => 250000, 'jumlah_ranjang' => 3,
        ]);

        $beds = $ruangan->beds()->get();
        $this->assertCount(3, $beds);
        $this->assertSame([1, 2, 3], $beds->pluck('nomor_ranjang')->all());
        $this->assertSame('Ruang Melati - Ranjang 1', $beds->first()->nama);
        $this->assertSame('VIP', $beds->first()->kelas);
        $this->assertEquals(250000, $beds->first()->tarif_per_hari);
        $this->assertSame('kosong', $beds->first()->status);
    }

    public function test_create_ruangan_rejects_bed_count_outside_valid_range(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->helper->createRuangan(['nama' => 'Ruang X', 'jumlah_ranjang' => 0]);
    }

    public function test_update_ruangan_cascades_rate_and_class_to_existing_beds(): void
    {
        $ruangan = $this->helper->createRuangan(['nama' => 'Ruang A', 'kelas' => 'Kelas 2', 'tarif_per_hari' => 100000, 'jumlah_ranjang' => 2]);

        $this->helper->updateRuangan($ruangan->id, ['nama' => 'Ruang A', 'kelas' => 'VIP', 'tarif_per_hari' => 300000]);

        $beds = $ruangan->beds()->get();
        $this->assertTrue($beds->every(fn ($bed) => $bed->kelas === 'VIP' && (float) $bed->tarif_per_hari === 300000.0));
    }

    public function test_update_ruangan_increasing_bed_count_adds_new_empty_beds(): void
    {
        $ruangan = $this->helper->createRuangan(['nama' => 'Ruang B', 'tarif_per_hari' => 100000, 'jumlah_ranjang' => 2]);

        $this->helper->updateRuangan($ruangan->id, ['jumlah_ranjang' => 4]);

        $beds = $ruangan->beds()->get();
        $this->assertCount(4, $beds);
        $this->assertSame([1, 2, 3, 4], $beds->pluck('nomor_ranjang')->all());
        $this->assertTrue($beds->every(fn ($bed) => $bed->status === 'kosong'));
    }

    public function test_update_ruangan_decreasing_bed_count_removes_highest_numbered_empty_beds(): void
    {
        $ruangan = $this->helper->createRuangan(['nama' => 'Ruang C', 'tarif_per_hari' => 100000, 'jumlah_ranjang' => 4]);

        $this->helper->updateRuangan($ruangan->id, ['jumlah_ranjang' => 2]);

        $beds = $ruangan->beds()->get();
        $this->assertCount(2, $beds);
        $this->assertSame([1, 2], $beds->pluck('nomor_ranjang')->all());
    }

    public function test_update_ruangan_rejects_decrease_when_a_bed_to_remove_is_occupied(): void
    {
        $ruangan = $this->helper->createRuangan(['nama' => 'Ruang D', 'tarif_per_hari' => 100000, 'jumlah_ranjang' => 2]);
        $lastBed = $ruangan->beds()->get()->last();
        $lastBed->update(['status' => 'terisi']);

        $this->expectException(InvalidArgumentException::class);
        $this->helper->updateRuangan($ruangan->id, ['jumlah_ranjang' => 1]);

        $this->assertCount(2, $ruangan->beds()->get());
    }

    public function test_delete_ruangan_removes_room_and_its_beds_when_all_empty(): void
    {
        $ruangan = $this->helper->createRuangan(['nama' => 'Ruang E', 'tarif_per_hari' => 100000, 'jumlah_ranjang' => 2]);
        $bedIds = $ruangan->beds()->pluck('id');

        $this->helper->deleteRuangan($ruangan->id);

        $this->assertNull(RuanganInap::find($ruangan->id));
        foreach ($bedIds as $bedId) {
            $this->assertNull(Kamar::find($bedId));
        }
    }

    public function test_delete_ruangan_rejects_when_any_bed_is_occupied(): void
    {
        $ruangan = $this->helper->createRuangan(['nama' => 'Ruang F', 'tarif_per_hari' => 100000, 'jumlah_ranjang' => 2]);
        $ruangan->beds()->first()->update(['status' => 'terisi']);

        $this->expectException(InvalidArgumentException::class);
        $this->helper->deleteRuangan($ruangan->id);
    }
}
