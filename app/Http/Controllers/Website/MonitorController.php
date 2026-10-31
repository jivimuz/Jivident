<?php

namespace App\Http\Controllers\Website;

use App\Helpers\RawatInapHelper;
use App\Http\Controllers\Controller;
use App\Models\DaftarPasien;
use App\Models\Setting;
use Illuminate\Http\Request;

class MonitorController extends Controller
{
    public function antrian(Request $request)
    {
        $urlVid = Setting::where('parameter', 'id_youtube_monitor')->first();
        // Papan monitor ini publik (tanpa login), jadi tidak ada Auth::user()->klienfk
        // untuk tahu cabang mana yang mau ditampilkan — tiap TV/perangkat monitor di
        // cabang tertentu harus dibuka dengan ?klienfk=<id cabang>, default ke cabang 1
        // untuk klinik yang belum pakai multi-cabang.
        $klienfk = $request->input('klienfk', 1);

        return view('public/monitor-antrian', compact('urlVid', 'klienfk'));
    }

    public function currentAntrian(Request $request)
    {
        $klienfk = $request->input('klienfk', 1);
        $today = now('Asia/Jakarta')->toDateString();

        // Ambil pasien yang statusnya 'Di Periksa' hari ini
        $current = DaftarPasien::select([
            't_daftarpasien.noantrian',
            'a.display_name as dokter',
            'b.name as ruangan',
            't_daftarpasien.tgldiperiksa',
        ])
            ->where('t_daftarpasien.klienfk', $klienfk)
            ->where('status', 'Di Periksa')
            ->whereDate('tgldiperiksa', $today)
            ->leftJoin('m_users as a', 't_daftarpasien.dokterfk', 'a.id')
            ->leftJoin('m_ruangpraktek as b', 't_daftarpasien.ruanganfk', 'b.id')
            ->orderBy('tgldiperiksa', 'desc')
            ->first();

        if (!$current) {
            return response()->json([
                'status' => 'empty'
            ]);
        }

        // Ambil 3 history sebelumnya hari ini juga
        $history = DaftarPasien::select([
            't_daftarpasien.noantrian',
            'b.name as ruangan',
            't_daftarpasien.tgldiperiksa',
        ])
            ->where('t_daftarpasien.klienfk', $klienfk)
            ->where('status', '!=', 'Dalam Antrian') // sesuaikan kalau status history beda
            ->whereDate('tgldiperiksa', $today)
            ->where('noantrian', '!=', $current->noantrian)
            ->leftJoin('m_ruangpraktek as b', 't_daftarpasien.ruanganfk', 'b.id')
            ->orderBy('tgldiperiksa', 'desc')
            ->limit(3)
            ->get();

        return response()->json([
            'status'  => 'ok',
            'current' => [
                'nomor'   => $current->noantrian,
                'ruangan' => $current->ruangan,
                'dokter'  => $current->dokter,
                'tgldiperiksa'  => $current->tgldiperiksa,
            ],
            'history' => $history->map(function ($item) {
                return [
                    'nomor'   => $item->noantrian,
                    'ruangan' => $item->ruangan,
                ];
            }),
        ]);
    }

    /**
     * Public room-occupancy board ("Monitor Ruangan") — same no-login,
     * ?klienfk=-scoped pattern as antrian() above, for a TV/screen at a
     * nurse station or corridor showing who's in which bed right now.
     * Deliberately shows patient name + No. RM per occupied bed (app
     * owner's explicit choice, not a safer anonymized default) rather than
     * hiding it behind a click like the staff-only /rawat-inap grid does.
     */
    public function ruangan(Request $request)
    {
        $klienfk = $request->input('klienfk', 1);

        return view('public/monitor-ruangan', compact('klienfk'));
    }

    public function roomData(Request $request)
    {
        $klienfk = (int) $request->input('klienfk', 1);

        $data = app(RawatInapHelper::class)->getRuanganListData($klienfk);

        return response()->json(['status' => 'ok', 'data' => $data]);
    }
}
