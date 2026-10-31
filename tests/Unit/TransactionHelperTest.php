<?php

namespace Tests\Unit;

use App\Helpers\TransactionHelper;
use App\Models\DaftarPasien;
use App\Models\TindakanPasien;
use App\Models\Transactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class TransactionHelperTest extends TestCase
{
    use RefreshDatabase;

    private TransactionHelper $helper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->helper = new TransactionHelper;
    }

    private function makeDaftarPasien(array $overrides = []): DaftarPasien
    {
        return DaftarPasien::create(array_merge([
            'klienfk' => 1,
            'noregistrasi' => 'REG-'.uniqid(),
            'ruanganfk' => 1,
            'pasienfk' => 1,
            'tglregistrasi' => now(),
            'noantrian' => 'A1',
            'biaya_registrasi' => 50000,
        ], $overrides));
    }

    public function test_rejects_invalid_transaction_type(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->helper->createTransaction([
            'transaction_type' => 'invalid',
            'typecode' => 'MISC',
            'amount' => 10000,
        ]);
    }

    public function test_rejects_empty_typecode(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->helper->createTransaction([
            'transaction_type' => 'in',
            'typecode' => '   ',
            'amount' => 10000,
        ]);
    }

    public function test_rejects_non_numeric_amount(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->helper->createTransaction([
            'transaction_type' => 'in',
            'typecode' => 'MISC',
            'amount' => 'abc',
        ]);
    }

    public function test_rejects_zero_or_negative_amount(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->helper->createTransaction([
            'transaction_type' => 'in',
            'typecode' => 'MISC',
            'amount' => 0,
        ]);
    }

    public function test_rejects_unknown_daftar_pasien(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->helper->createTransaction([
            'transaction_type' => 'in',
            'typecode' => 'MISC',
            'amount' => 10000,
            'daftarpasien_id' => 999999,
        ]);
    }

    public function test_creates_transaction_without_patient(): void
    {
        $transaction = $this->helper->createTransaction([
            'transaction_type' => 'out',
            'typecode' => 'MISC',
            'amount' => 15000,
            'notes' => 'Beli ATK',
        ]);

        $this->assertInstanceOf(Transactions::class, $transaction);
        $this->assertDatabaseHas('t_transactions', [
            'id' => $transaction->id,
            'transaction_type' => 'out',
            'daftarpasien_id' => null,
        ]);
    }

    public function test_full_payment_marks_verifikasi_without_touching_visit_status(): void
    {
        $daftarPasien = $this->makeDaftarPasien();

        // tagihan tindakan 50.000 + biaya registrasi default (setting) 50.000 = 100.000
        TindakanPasien::create([
            'fk_daftarpasien' => $daftarPasien->id,
            'tindakan' => 'Scaling',
            'dokter' => 'drg. Test',
            'qty' => 1,
            'price' => 50000,
            'amount' => 50000,
        ]);

        $this->helper->createTransaction([
            'transaction_type' => 'in',
            'typecode' => 'PAY',
            'amount' => 100000,
            'daftarpasien_id' => $daftarPasien->id,
            'fk_payment' => 1, // Cash (seeded by migration)
        ]);

        $daftarPasien->refresh();

        // Status pembayaran dan status kunjungan independen — lunas tidak
        // otomatis menandai kunjungan "Selesai" (itu cuma terjadi lewat
        // PemeriksaanController::updateStatus() saat pemeriksaan ditutup).
        $this->assertSame('Verifikasi', $daftarPasien->statuspembayaran);
        $this->assertSame('Dalam Antrian', $daftarPasien->status);
        $this->assertDatabaseHas('t_billingtransactions', [
            'daftarpasien_id' => $daftarPasien->id,
            'amount' => 100000,
        ]);
    }

    public function test_partial_payment_marks_verifikasi_sebagian_and_keeps_visit_open(): void
    {
        $daftarPasien = $this->makeDaftarPasien();

        // tagihan tindakan 100.000 + biaya registrasi default 50.000 = 150.000
        TindakanPasien::create([
            'fk_daftarpasien' => $daftarPasien->id,
            'tindakan' => 'Cabut Gigi',
            'dokter' => 'drg. Test',
            'qty' => 1,
            'price' => 100000,
            'amount' => 100000,
        ]);

        $this->helper->createTransaction([
            'transaction_type' => 'in',
            'typecode' => 'PAY',
            'amount' => 50000,
            'daftarpasien_id' => $daftarPasien->id,
            'fk_payment' => 1,
        ]);

        $daftarPasien->refresh();

        $this->assertSame('Verifikasi Sebagian', $daftarPasien->statuspembayaran);
        $this->assertNotSame('Selesai', $daftarPasien->status);
    }
}
