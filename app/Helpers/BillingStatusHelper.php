<?php

namespace App\Helpers;

use App\Models\DaftarPasien;
use Illuminate\Support\Facades\DB;

/** Keeps payment status in sync whenever a billable clinical item changes. */
final class BillingStatusHelper
{
    public function refresh(int $daftarPasienId): ?string
    {
        $daftarPasien = DaftarPasien::find($daftarPasienId);
        if (! $daftarPasien) {
            return null;
        }

        $totalTagihan = (float) $daftarPasien->biaya_registrasi
            + $this->sum('t_tindakanpasien', 'amount', $daftarPasienId)
            + $this->sum('t_diagnosapasien', 'amount', $daftarPasienId)
            + $this->sum('t_labpasien', 'harga_snapshot', $daftarPasienId)
            + $this->sum('t_radiologipasien', 'harga_snapshot', $daftarPasienId);

        $totalPaid = (float) DB::table('t_transactions')
            ->where('daftarpasien_id', $daftarPasienId)
            ->whereNull('deleted_at')
            ->selectRaw("COALESCE(SUM(CASE WHEN transaction_type = 'in' THEN amount WHEN transaction_type = 'out' THEN -amount ELSE 0 END), 0) AS total_paid")
            ->value('total_paid');

        $status = $this->resolve($totalPaid, $totalTagihan, $daftarPasien->statuspembayaran);
        if ($daftarPasien->statuspembayaran !== $status) {
            $daftarPasien->update(['statuspembayaran' => $status]);
        }

        return $status;
    }

    private function sum(string $table, string $column, int $daftarPasienId): float
    {
        return (float) DB::table($table)
            ->where('fk_daftarpasien', $daftarPasienId)
            ->whereNull('deleted_at')
            ->sum($column);
    }

    private function resolve(float $totalPaid, float $totalTagihan, ?string $statusSebelumnya): string
    {
        if ($totalTagihan <= 0 || $totalPaid <= 0) {
            return 'Belum Verifikasi';
        }

        if ($totalPaid + 0.000001 < $totalTagihan) {
            return 'Verifikasi Sebagian';
        }

        // "Lunas" adalah penutupan pembayaran yang sudah ada. Pertahankan
        // hanya selama total pembayaran masih benar-benar mencukupi.
        return $statusSebelumnya === 'Lunas' ? 'Lunas' : 'Verifikasi';
    }
}
