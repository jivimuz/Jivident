<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Models\BillingTransaction;
use App\Models\DaftarPasien;
use App\Models\Patient;
use App\Models\Payments;
use App\Models\Transactions;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

final class TransactionHelper
{
    /**
     * @param array $data
     * @return Transactions
     */
    public function createTransaction(array $data): Transactions
    {
        // ====== Guard minimal biar gak “nyasar” ======
        $transactionType = $data['transaction_type'] ?? null;
        if (!in_array($transactionType, ['in', 'out'], true)) {
            throw new InvalidArgumentException("transaction_type harus 'in' atau 'out'");
        }

        $typecode = trim((string)($data['typecode'] ?? ''));
        if ($typecode === '') {
            throw new InvalidArgumentException("typecode wajib diisi");
        }

        $amountRaw = $data['amount'] ?? null;
        if ($amountRaw === null || $amountRaw === '' || !is_numeric($amountRaw)) {
            throw new InvalidArgumentException("amount wajib numeric");
        }

        // Hindari float untuk money (tetap simpan numeric-string kalau kolom DB DECIMAL)
        $amount = (string)$amountRaw;
        if ((float)$amount <= 0) {
            throw new InvalidArgumentException("amount harus > 0");
        }


        $daftarPasienID = $data['daftarpasien_id'] ?? null; // kemungkinan ini
        $daftarPasienID = ($daftarPasienID === '' || $daftarPasienID === 0) ? null : $daftarPasienID;


        if (!empty($daftarPasienID) && !DaftarPasien::whereKey($daftarPasienID)->exists()) {
            throw new InvalidArgumentException("Daftar Pasien tidak ditemukan");
        }
        $daftarPasien = null;

        if (!empty($daftarPasienID)) {
            $daftarPasien = DaftarPasien::query()
                ->whereKey($daftarPasienID)
                ->lockForUpdate()
                ->first();

            if (!$daftarPasien) {
                throw new InvalidArgumentException("Daftar Pasien tidak ditemukan");
            }

            $tstatus = $daftarPasien->status;
            // dd($tstatus);

        }
        // ====== Atomic: kalau gagal, rollback semua ======
        return DB::transaction(function () use ($data, $amount,  $daftarPasienID,  $daftarPasien, $transactionType, $typecode) {



            $transaction = Transactions::query()->create([
                'no_transaction'   => $this->generateTransactionNumber('TRX'),
                'pasien_id'       => $daftarPasien->pasienfk ?? null,
                'daftarpasien_id'  => $daftarPasienID,
                'transaction_type' => $transactionType,
                'typecode'         => $typecode,
                'amount'           => $amount,
                'notes'            => $data['notes'] ?? null,
                'userhandler_id'   => $data['userhandler_id'] ?? null,
                'is_refund'        => (bool)($data['is_refund'] ?? false),
                'attachment_path'  => $data['attachment_path'] ?? null,
                'attachment_type'  => $data['attachment_type'] ?? ($data['attachmentType'] ?? null),
                'created_at' =>  Carbon::now(),
            ]);

            // ====== Kalau ada pasien_id, sync ke payment + update status ======
            if (!empty($daftarPasienID)) {

                $payment = Payments::where('id', $data['fk_payment'])->first();
                BillingTransaction::query()->create([
                    'klienfk' => Auth::user()->klienfk ?? 1,
                    'transaction_id' => $transaction->id,
                    'no_billing'   => $this->generateTransactionNumber('BLX'),
                    'daftarpasien_id'   => $daftarPasienID,
                    'pasien_id'     =>  $daftarPasien->pasienfk,
                    'amount'         => $amount,
                    'fk_payment' =>  $payment->id ?? null,
                    'payment_title' =>  $payment->payment_title ?? null,
                    'payment_content' =>  $payment->payment_content ?? null,
                    'payment_image_url' =>  $payment->payment_image_url ?? null,
                    'payment_is_qr' =>  $payment->payment_is_qr ?? null,
                    'type' =>  $transactionType,
                    'created_at' =>  Carbon::now(),
                ]);

                // Hitung total paid signed (in = +, out = -)
                // NOTE: kalau transaksi  kamu ada yang bukan pembayaran tapi tetap punya pasien_id,
                // sebaiknya filter typecode tertentu di sini.
                $table = $transaction->getTable();

                $totalPaid = (float) DB::table($table)
                    ->where('daftarpasien_id', $daftarPasienID)
                    ->selectRaw("COALESCE(SUM(CASE
                        WHEN transaction_type='in' THEN amount
                        WHEN transaction_type='out' THEN -amount
                        ELSE 0 END),0) as total_paid")
                    ->value('total_paid');

                $tagihanTindakan = (float) DB::table('t_tindakanpasien')
                    ->where('fk_daftarpasien', $daftarPasienID)
                    ->selectRaw("COALESCE(SUM(amount),0) as total_amount")
                    ->value('total_amount');;

                $price = (float)($tagihanTindakan ?? 0);

                $newStatus = $this->resolvePaymentStatus($totalPaid, $price);

                // Update status kalau berubah
                if (($daftarPasien->statuspembayaran ?? null) !== $newStatus) {
                    if ($newStatus == 'Verifikasi') {
                        $daftarPasien->status = 'Selesai';
                    }
                    $daftarPasien->statuspembayaran = $newStatus;
                    $daftarPasien->save();
                }
            }

            return $transaction;
        }, 3); // retry 3x untuk kasus deadlock
    }


    protected function resolvePaymentStatus(float $totalPaid, float $price): string
    {
        if ($price <= 0) {
            return 'Belum Verifikasi';
        }

        if ($totalPaid <= 0) {
            return 'Belum Verifikasi';
        }

        // Toleransi kecil buat decimal rounding
        if ($totalPaid + 0.000001 < $price) {
            return 'Verifikasi Sebagian';
        }

        return 'Verifikasi';
    }

    protected function generateTransactionNumber(string $prefix, int $maxAttempts = 5): string
    {
        $now    = CarbonImmutable::now(); // ikut APP_TIMEZONE kalau config bener

        for ($i = 0; $i < $maxAttempts; $i++) {
            $rand = Str::upper(Str::random(8));
            $no   = "{$prefix}{$now->format('Ymd')}{$rand}{$now->format('His')}";
            if ($prefix == 'TRX') {
                if (!Transactions::query()->where('no_transaction', $no)->exists()) {
                    return $no;
                }
            } else {
                if (!BillingTransaction::query()->where('no_billing', $no)->exists()) {
                    return $no;
                }
            }
        }

        // fallback super-unik
        return "{$prefix}{$now->format('Ymd')}" . Str::upper(str_replace('-', '', (string) Str::uuid()));
    }
}
