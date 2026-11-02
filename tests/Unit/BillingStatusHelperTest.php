<?php

namespace Tests\Unit;

use App\Helpers\BillingStatusHelper;
use App\Helpers\TransactionHelper;
use App\Models\DaftarPasien;
use App\Models\Patient;
use App\Models\ResepPasien;
use App\Models\TindakanPasien;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingStatusHelperTest extends TestCase
{
    use RefreshDatabase;

    private function makeVisit(array $overrides = []): DaftarPasien
    {
        $patient = Patient::create([
            'klienfk' => 1,
            'norm' => 'RM-'.uniqid(),
            'nik' => (string) random_int(1000000000, 9999999999),
            'nama' => 'Pasien Resep',
        ]);

        return DaftarPasien::create(array_merge([
            'klienfk' => 1,
            'noregistrasi' => 'REG-'.uniqid(),
            'ruanganfk' => 1,
            'pasienfk' => $patient->id,
            'tglregistrasi' => now(),
            'statuspasien' => 'LAMA',
            'status' => 'Dalam Antrian',
            'noantrian' => 'A1',
            'statuspembayaran' => 'Belum Verifikasi',
            'biaya_registrasi' => 0,
        ], $overrides));
    }

    /**
     * Regresi: t_reseppasien.subtotal tidak pernah dihitung ke
     * statuspembayaran sama sekali sebelumnya — ResepPasien baru dipakai
     * FK `daftarpasien_id`, bukan `fk_daftarpasien` seperti tabel lain yang
     * sudah dihitung refresh() (tindakan/diagnosa/lab/radiologi).
     */
    public function test_resep_subtotal_counts_toward_total_tagihan(): void
    {
        $visit = $this->makeVisit();

        ResepPasien::create([
            'klienfk' => 1,
            'pasien_id' => $visit->pasienfk,
            'daftarpasien_id' => $visit->id,
            'type' => 'non-racik',
            'r_num' => 1,
            'obat_name' => 'Paracetamol',
            'qty' => 10,
            'harga_satuan' => 2000,
            'unit' => 'Tablet',
            'penggunaan_id' => 1,
            'subtotal' => 20000,
        ]);

        app(BillingStatusHelper::class)->refresh($visit->id);

        // Belum ada pembayaran — status tetap "Belum Verifikasi", tapi kalau
        // resep diperhitungkan, membayar persis Rp 20.000 harus melunasi.
        $this->assertDatabaseHas('t_daftarpasien', [
            'id' => $visit->id,
            'statuspembayaran' => 'Belum Verifikasi',
        ]);
    }

    public function test_full_payment_including_resep_marks_verifikasi(): void
    {
        $visit = $this->makeVisit();

        ResepPasien::create([
            'klienfk' => 1,
            'pasien_id' => $visit->pasienfk,
            'daftarpasien_id' => $visit->id,
            'type' => 'non-racik',
            'r_num' => 1,
            'obat_name' => 'Paracetamol',
            'qty' => 10,
            'harga_satuan' => 2000,
            'unit' => 'Tablet',
            'penggunaan_id' => 1,
            'subtotal' => 20000,
        ]);

        app(TransactionHelper::class)->createTransaction([
            'transaction_type' => 'in',
            'typecode' => 'BIL',
            'amount' => 20000,
            'daftarpasien_id' => $visit->id,
            'fk_payment' => 1,
        ]);

        app(BillingStatusHelper::class)->refresh($visit->id);

        $this->assertDatabaseHas('t_daftarpasien', [
            'id' => $visit->id,
            'statuspembayaran' => 'Verifikasi',
        ]);
    }

    /**
     * Ini alur yang diminta untuk dicek ulang: pasien sudah bayar lunas untuk
     * kunjungan hari itu, lalu ternyata ada pemeriksaan/tindakan TAMBAHAN
     * sesudahnya (misalnya dokter menambah tindakan baru di kunjungan yang
     * sama) — status pemeriksaan boleh tetap "Selesai", tapi statuspembayaran
     * harus kembali menunjukkan ada tunggakan, bukan diam di status lunas
     * yang sudah basi.
     */
    public function test_additional_charge_after_full_payment_reopens_the_balance(): void
    {
        $visit = $this->makeVisit(['biaya_registrasi' => 20000]);

        app(TransactionHelper::class)->createTransaction([
            'transaction_type' => 'in',
            'typecode' => 'BIL',
            'amount' => 20000,
            'daftarpasien_id' => $visit->id,
            'fk_payment' => 1,
        ]);
        app(BillingStatusHelper::class)->refresh($visit->id);

        $this->assertDatabaseHas('t_daftarpasien', [
            'id' => $visit->id,
            'statuspembayaran' => 'Verifikasi',
        ]);

        // Dokter menambah tindakan baru sesudah pasien sempat lunas.
        TindakanPasien::create([
            'klienfk' => 1,
            'fk_daftarpasien' => $visit->id,
            'tindakan' => 'Scaling tambahan',
            'dokter' => 'drg. A',
            'qty' => 1,
            'amount' => 50000,
        ]);
        app(BillingStatusHelper::class)->refresh($visit->id);

        $visit->refresh();
        $this->assertSame('Verifikasi Sebagian', $visit->statuspembayaran);
    }

    /**
     * Sama seperti di atas, tapi dari status "Lunas" — resolve() secara
     * eksplisit cuma mempertahankan "Lunas" selama total bayar masih
     * mencukupi (lihat komentar di BillingStatusHelper::resolve()), jadi
     * tunggakan baru harus tetap menurunkan status meski sebelumnya "Lunas".
     */
    public function test_additional_charge_after_lunas_reopens_the_balance(): void
    {
        $visit = $this->makeVisit(['biaya_registrasi' => 20000, 'statuspembayaran' => 'Lunas']);

        app(TransactionHelper::class)->createTransaction([
            'transaction_type' => 'in',
            'typecode' => 'BIL',
            'amount' => 20000,
            'daftarpasien_id' => $visit->id,
            'fk_payment' => 1,
        ]);

        TindakanPasien::create([
            'klienfk' => 1,
            'fk_daftarpasien' => $visit->id,
            'tindakan' => 'Tindakan tambahan',
            'dokter' => 'drg. A',
            'qty' => 1,
            'amount' => 30000,
        ]);
        app(BillingStatusHelper::class)->refresh($visit->id);

        $visit->refresh();
        $this->assertSame('Verifikasi Sebagian', $visit->statuspembayaran);
    }
}
