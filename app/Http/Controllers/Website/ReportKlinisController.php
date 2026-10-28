<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportKlinisController extends Controller
{
    public function index()
    {
        return view('pages/report_klinis/index');
    }

    public function getTrendKunjungan(Request $request)
    {
        $klienfk = Auth::user()->klienfk;
        [$start, $end] = $this->resolveDateRange($request);

        $rows = DB::table('t_daftarpasien')
            ->where('klienfk', $klienfk)
            ->where('status', '!=', 'Dibatalkan')
            ->whereDate('tglregistrasi', '>=', $start)
            ->whereDate('tglregistrasi', '<=', $end)
            ->select('tglregistrasi', 'statuspasien')
            ->get();

        $byDate = [];
        foreach ($rows as $row) {
            $date = Carbon::parse($row->tglregistrasi)->toDateString();
            $byDate[$date] ??= ['tanggal' => $date, 'baru' => 0, 'lama' => 0, 'total' => 0];
            if ($row->statuspasien === 'BARU') {
                $byDate[$date]['baru']++;
            } else {
                $byDate[$date]['lama']++;
            }
            $byDate[$date]['total']++;
        }
        ksort($byDate);

        return response()->json(['message' => 'success', 'data' => array_values($byDate)], 200);
    }

    public function getDiagnosaTerbanyak(Request $request)
    {
        $klienfk = Auth::user()->klienfk;
        [$start, $end] = $this->resolveDateRange($request);

        $data = DB::table('t_diagnosapasien')
            ->where('klienfk', $klienfk)
            ->whereNull('deleted_at')
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $end)
            ->selectRaw('diagnosa, COUNT(*) as jumlah')
            ->groupBy('diagnosa')
            ->orderByDesc('jumlah')
            ->limit(10)
            ->get();

        return response()->json(['message' => 'success', 'data' => $data], 200);
    }

    /**
     * Rekap payroll TIDAK difilter klienfk — tabel karyawan/payroll di app ini
     * belum tenant-scoped sama sekali (konsisten dengan PayrollController/
     * EmployeesController yang juga tidak filter klienfk).
     */
    public function getRekapPayroll(Request $request)
    {
        $periode = $request->periode ?? now()->format('Y-m');

        $rows = DB::table('t_payroll_slips as s')
            ->join('m_employee as e', 'e.id', 's.fk_employee')
            ->where('s.periode', $periode)
            ->select(
                'e.fullname',
                's.gaji_pokok',
                's.bpjs_kesehatan_company',
                's.bpjs_kesehatan_employee',
                's.bpjs_jht_company',
                's.bpjs_jht_employee',
                's.bpjs_jkk',
                's.bpjs_jkm',
                's.bpjs_jp_company',
                's.bpjs_jp_employee',
                's.pph21_nominal',
                's.gaji_bersih'
            )
            ->orderBy('e.fullname')
            ->get();

        $totalBebanPerusahaan = $rows->sum(fn ($r) => (float) $r->gaji_pokok
            + (float) $r->bpjs_kesehatan_company
            + (float) $r->bpjs_jht_company
            + (float) $r->bpjs_jkk
            + (float) $r->bpjs_jkm
            + (float) $r->bpjs_jp_company);

        return response()->json([
            'message' => 'success',
            'periode' => $periode,
            'data' => $rows,
            'total_gaji_bersih' => $rows->sum('gaji_bersih'),
            'total_beban_perusahaan' => $totalBebanPerusahaan,
        ], 200);
    }

    /**
     * Sama seperti getRekapPayroll, tidak difilter klienfk (lihat catatan di atas).
     */
    public function getKehadiranCuti(Request $request)
    {
        [$start, $end] = $this->resolveDateRange($request);

        $shifts = DB::table('t_jadwalkerjakaryawan as j')
            ->join('m_employee as e', 'e.id', 'j.fk_employee')
            ->whereNull('j.deleted_at')
            ->whereDate('j.tanggal', '>=', $start)
            ->whereDate('j.tanggal', '<=', $end)
            ->select('e.fullname', 'j.jam_masuk', 'j.jam_pulang')
            ->get();

        $kehadiran = [];
        foreach ($shifts as $shift) {
            $kehadiran[$shift->fullname] ??= ['fullname' => $shift->fullname, 'jumlah_shift' => 0, 'total_jam' => 0.0];
            $kehadiran[$shift->fullname]['jumlah_shift']++;
            $menit = Carbon::parse($shift->jam_masuk)->diffInMinutes(Carbon::parse($shift->jam_pulang));
            $kehadiran[$shift->fullname]['total_jam'] += round($menit / 60, 2);
        }

        $cutiRows = DB::table('t_cuti as c')
            ->join('m_employee as e', 'e.id', 'c.fk_employee')
            ->leftJoin('m_jeniscuti as jc', 'jc.id', 'c.fk_jeniscuti')
            ->where('c.status', 'approved')
            ->whereDate('c.startdate', '<=', $end)
            ->whereDate('c.enddate', '>=', $start)
            ->select('e.fullname', 'c.startdate', 'c.enddate')
            ->get();

        $cuti = [];
        foreach ($cutiRows as $row) {
            $hari = Carbon::parse($row->startdate)->diffInDays(Carbon::parse($row->enddate)) + 1;
            $cuti[$row->fullname] ??= ['fullname' => $row->fullname, 'jumlah_pengajuan' => 0, 'total_hari' => 0];
            $cuti[$row->fullname]['jumlah_pengajuan']++;
            $cuti[$row->fullname]['total_hari'] += $hari;
        }

        return response()->json([
            'message' => 'success',
            'kehadiran' => array_values($kehadiran),
            'cuti' => array_values($cuti),
        ], 200);
    }

    private function resolveDateRange(Request $request): array
    {
        $start = $request->start_date ?? now()->startOfMonth()->toDateString();
        $end = $request->end_date ?? now()->toDateString();

        return [$start, $end];
    }
}
