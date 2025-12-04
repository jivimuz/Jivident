<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\DaftarPasien;
use App\Models\Setting;
use Illuminate\Http\Request;

class MonitorController extends Controller
{
    public function antrian()
    {
        $urlVid = Setting::where('parameter', 'id_youtube_monitor')->first();

        return view('public/monitor-antrian', compact('urlVid'));
    }

    public function currentAntrian()
    {
        $today = now('Asia/Jakarta')->toDateString();

        // Ambil pasien yang statusnya 'Di Periksa' hari ini
        $current = DaftarPasien::select([
            't_daftarpasien.noantrian',
            'a.display_name as dokter',
            'b.name as ruangan',
            't_daftarpasien.tgldiperiksa',
        ])
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
}
