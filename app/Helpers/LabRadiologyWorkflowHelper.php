<?php

namespace App\Helpers;

use App\Models\LabPasien;
use App\Models\RadiologiPasien;
use Illuminate\Support\Str;
use InvalidArgumentException;

class LabRadiologyWorkflowHelper
{
    public static function collectSpecimen(LabPasien $order, int $userId): LabPasien
    {
        if ($order->status !== 'diminta' || $order->specimen_collected_at) {
            throw new InvalidArgumentException('Spesimen tidak dapat diambil pada status saat ini.');
        }

        $order->update([
            'specimen_code' => 'SP-'.now()->format('YmdHis').'-'.Str::upper(Str::random(5)),
            'specimen_collected_at' => now(),
            'specimen_collected_by' => $userId,
        ]);

        return $order->refresh();
    }

    public static function verifyLabResult(LabPasien $order, int $userId): LabPasien
    {
        if (! $order->hasil || $order->status !== 'selesai') {
            throw new InvalidArgumentException('Hasil laboratorium belum siap diverifikasi.');
        }

        $order->update([
            'verification_status' => 'verified',
            'verified_by' => $userId,
            'verified_at' => now(),
        ]);

        return $order->refresh();
    }

    public static function approveRadiologyReport(RadiologiPasien $order, int $userId): RadiologiPasien
    {
        if ($order->status !== 'selesai' || ! $order->hasil_interpretasi) {
            throw new InvalidArgumentException('Laporan radiologi belum siap disetujui.');
        }

        $order->update([
            'report_status' => 'approved',
            'report_approved_by' => $userId,
            'report_approved_at' => now(),
        ]);

        return $order->refresh();
    }
}
