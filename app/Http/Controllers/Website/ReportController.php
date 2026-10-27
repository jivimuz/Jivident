<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('pages/report/index');
    }

    public function getOmsetBulanan(Request $request)
    {
        $year = (int) ($request->year ?? now()->year);
        $klienfk = Auth::user()->klienfk;

        // Dijumlah per bulan di PHP (bukan MONTH() SQL) supaya portable — MONTH()
        // tidak ada di SQLite (dipakai saat test), hanya di MySQL (produksi).
        $rows = DB::table('t_transactions')
            ->where('klienfk', $klienfk)
            ->whereNull('deleted_at')
            ->whereYear('created_at', $year)
            ->select('created_at', 'transaction_type', 'amount')
            ->get();

        $totalsByMonth = array_fill(1, 12, 0.0);
        foreach ($rows as $row) {
            $month = Carbon::parse($row->created_at)->month;
            $signed = $row->transaction_type === 'in' ? (float) $row->amount : -(float) $row->amount;
            $totalsByMonth[$month] += $signed;
        }

        $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $data = [];
        for ($m = 1; $m <= 12; $m++) {
            $data[] = [
                'bulan' => $labels[$m - 1],
                'total' => $totalsByMonth[$m],
            ];
        }

        return response()->json(['message' => 'success', 'year' => $year, 'data' => $data], 200);
    }

    public function getTindakanTerlaris(Request $request)
    {
        [$start, $end] = $this->resolveDateRange($request);
        $klienfk = Auth::user()->klienfk;

        $data = DB::table('t_tindakanpasien')
            ->where('klienfk', $klienfk)
            ->whereNull('deleted_at')
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $end)
            ->selectRaw('tindakan, COUNT(*) as jumlah, COALESCE(SUM(amount), 0) as total_pendapatan')
            ->groupBy('tindakan')
            ->orderByDesc('jumlah')
            ->limit(10)
            ->get();

        return response()->json(['message' => 'success', 'data' => $data], 200);
    }

    public function getPerformaDokter(Request $request)
    {
        [$start, $end] = $this->resolveDateRange($request);
        $klienfk = Auth::user()->klienfk;

        $data = DB::table('t_tindakanpasien')
            ->where('klienfk', $klienfk)
            ->whereNull('deleted_at')
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $end)
            ->whereNotNull('dokter')
            ->selectRaw('dokter, COUNT(*) as jumlah_tindakan, COALESCE(SUM(amount), 0) as total_pendapatan')
            ->groupBy('dokter')
            ->orderByDesc('total_pendapatan')
            ->get();

        return response()->json(['message' => 'success', 'data' => $data], 200);
    }

    public function getDemografiPasien(Request $request)
    {
        $klienfk = Auth::user()->klienfk;

        $genderRows = DB::table('m_pasien')
            ->leftJoin('m_jeniskelamin', 'm_jeniskelamin.id', 'm_pasien.jeniskelaminfk')
            ->where('m_pasien.klienfk', $klienfk)
            ->selectRaw("COALESCE(m_jeniskelamin.jeniskelamin, 'Tidak diketahui') as label, COUNT(*) as jumlah")
            ->groupBy('label')
            ->get();

        $birthdates = DB::table('m_pasien')
            ->where('klienfk', $klienfk)
            ->whereNotNull('tgllahir')
            ->pluck('tgllahir');

        $buckets = ['0-17' => 0, '18-30' => 0, '31-45' => 0, '46-60' => 0, '60+' => 0];
        foreach ($birthdates as $tgl) {
            $age = Carbon::parse($tgl)->age;
            if ($age <= 17) {
                $buckets['0-17']++;
            } elseif ($age <= 30) {
                $buckets['18-30']++;
            } elseif ($age <= 45) {
                $buckets['31-45']++;
            } elseif ($age <= 60) {
                $buckets['46-60']++;
            } else {
                $buckets['60+']++;
            }
        }

        return response()->json([
            'message' => 'success',
            'data' => [
                'gender' => $genderRows,
                'usia' => $buckets,
            ],
        ], 200);
    }

    private function resolveDateRange(Request $request): array
    {
        $start = $request->start_date ?? now()->startOfMonth()->toDateString();
        $end = $request->end_date ?? now()->toDateString();

        return [$start, $end];
    }
}
