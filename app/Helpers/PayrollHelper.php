<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Models\Employee;
use App\Models\PayrollSlip;
use App\Models\Ptkp;
use App\Models\Setting;
use App\Models\TerBracket;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;
use RuntimeException;

/**
 * Monthly payroll slip calculator: BPJS components (rates/caps fully
 * configurable via Setting, seeded at 0 — nothing assumed) + PPh21 via the
 * TER (Tarif Efektif Rata-rata) method per PMK 168/2023. See the
 * m_ter_brackets seed migration for a note on the TER table's source/caveats.
 */
final class PayrollHelper
{
    public function calculateBpjs(float $gajiPokok): array
    {
        $pct = fn (string $param) => (float) (Setting::where('parameter', $param)->value('value') ?? 0);

        $kesehatanCap = $pct('bpjs_kesehatan_cap');
        $kesehatanBase = $kesehatanCap > 0 ? min($gajiPokok, $kesehatanCap) : $gajiPokok;

        $jpCap = $pct('bpjs_jp_cap');
        $jpBase = $jpCap > 0 ? min($gajiPokok, $jpCap) : $gajiPokok;

        return [
            'bpjs_kesehatan_company' => round($kesehatanBase * $pct('bpjs_kesehatan_company_persen') / 100, 2),
            'bpjs_kesehatan_employee' => round($kesehatanBase * $pct('bpjs_kesehatan_employee_persen') / 100, 2),
            'bpjs_jht_company' => round($gajiPokok * $pct('bpjs_jht_company_persen') / 100, 2),
            'bpjs_jht_employee' => round($gajiPokok * $pct('bpjs_jht_employee_persen') / 100, 2),
            'bpjs_jkk' => round($gajiPokok * $pct('bpjs_jkk_persen') / 100, 2),
            'bpjs_jkm' => round($gajiPokok * $pct('bpjs_jkm_persen') / 100, 2),
            'bpjs_jp_company' => round($jpBase * $pct('bpjs_jp_company_persen') / 100, 2),
            'bpjs_jp_employee' => round($jpBase * $pct('bpjs_jp_employee_persen') / 100, 2),
        ];
    }

    public function calculatePph21(Employee $employee, float $grossMonthly): array
    {
        $ptkp = Ptkp::find($employee->fk_ptkp);
        if (! $ptkp || ! $ptkp->ter_kategori) {
            throw new InvalidArgumentException('Karyawan belum punya status PTKP yang valid (dengan kategori TER) — isi dulu di tab Statutory');
        }

        $bracket = TerBracket::where('kategori', $ptkp->ter_kategori)
            ->where('batas_bawah', '<', $grossMonthly)
            ->where(function ($q) use ($grossMonthly) {
                $q->whereNull('batas_atas')->orWhere('batas_atas', '>=', $grossMonthly);
            })
            ->first();

        if (! $bracket) {
            throw new RuntimeException('Tidak ada bracket TER yang cocok untuk penghasilan ini');
        }

        $nominal = round($grossMonthly * (float) $bracket->tarif_persen / 100, 2);

        return [
            'ptkp_status' => $ptkp->name,
            'ter_kategori' => $ptkp->ter_kategori,
            'pph21_persen' => (float) $bracket->tarif_persen,
            'pph21_nominal' => $nominal,
        ];
    }

    public function generateSlip(Employee $employee, string $periode, ?float $gajiPokokOverride = null): PayrollSlip
    {
        if (! preg_match('/^\d{4}-\d{2}$/', $periode)) {
            throw new InvalidArgumentException('Periode harus format YYYY-MM');
        }

        if (PayrollSlip::where('fk_employee', $employee->id)->where('periode', $periode)->exists()) {
            throw new InvalidArgumentException("Slip gaji untuk periode {$periode} sudah ada");
        }

        $gajiPokok = $gajiPokokOverride ?? (float) $employee->gajipokok;
        if ($gajiPokok <= 0) {
            throw new InvalidArgumentException('Gaji pokok harus lebih dari 0');
        }

        $bpjs = $this->calculateBpjs($gajiPokok);
        $pph = $this->calculatePph21($employee, $gajiPokok);

        $gajiBersih = $gajiPokok
            - $bpjs['bpjs_kesehatan_employee']
            - $bpjs['bpjs_jht_employee']
            - $bpjs['bpjs_jp_employee']
            - $pph['pph21_nominal'];

        return PayrollSlip::create(array_merge($bpjs, $pph, [
            'fk_employee' => $employee->id,
            'periode' => $periode,
            'gaji_pokok' => $gajiPokok,
            'gaji_bersih' => $gajiBersih,
            'created_by' => Auth::id(),
        ]));
    }
}
