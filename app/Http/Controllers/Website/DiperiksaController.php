<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\DaftarPasien;
use App\Models\RuangPraktek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiperiksaController extends Controller
{
    public function index()
    {
        $ruangan = RuangPraktek::select(['m_ruangpraktek.*', 'm_users.id as dokterid', 'm_users.display_name as dokter'])
            ->join('m_users', 'm_ruangpraktek.fk_dokter', 'm_users.id')
            ->where('m_ruangpraktek.klienfk', Auth::user()->klienfk ?? 1)->get();
        return view('pages/diperiksa/index', compact('ruangan'));
    }

    public function getList(Request $request)
    {
        $offset = $request->input('start', 0);
        $limit = $request->input('length', 10);
        $searchValue = $request->post('pasienSearch');
        $ruanganfk = $request->input('ruanganfk');
        $tgldiperiksa = $request->input('tgldiperiksa');
        $status = $request->input('status');

        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderDirection = $request->input('order.0.dir', 'desc');
        // $nik = $request->post('nik_search');

        $columns = [
            't_daftarpasien.noantrian',
            't_daftarpasien.tgldiperiksa',
            'b.name as ruangan',
            'c.display_name as dokter',
            'a.norm',
            't_daftarpasien.noregistrasi',
            'a.nama',
            't_daftarpasien.statuspasien',
            't_daftarpasien.status',
            't_daftarpasien.id',
            'a.nik',
        ];

        $orderColumnName = $columns[$orderColumnIndex] ?? 'id';

        // Base query
        $query = DaftarPasien::query()->select($columns);
        $query->leftJoin('m_pasien as a', 'a.id', 't_daftarpasien.pasienfk');
        $query->leftJoin('m_ruangpraktek as b', 'b.id', 't_daftarpasien.ruanganfk');
        $query->leftJoin('m_users as c', 'c.id', 't_daftarpasien.dokterfk');

        // Filter by NIK (kalau ada)
        if ($ruanganfk) {
            $query->where('t_daftarpasien.ruanganfk', $ruanganfk);
        }
        // Filter tanggal (support range)
        if (!empty($tgldiperiksa)) {
            $tglRange = explode(' to ', $tgldiperiksa);

            if (count($tglRange) === 2) {
                $query->whereBetween('t_daftarpasien.tgldiperiksa', [
                    trim($tglRange[0]) . ' 00:00:00',
                    trim($tglRange[1]) . ' 23:59:59',
                ]);
            } else {
                // Jika hanya 1 tanggal
                $query->whereDate('t_daftarpasien.tgldiperiksa', trim($tgldiperiksa));
            }
        }

        // Filter pencarian
        if ($searchValue) {
            $query->where(function ($q) use ($searchValue) {
                $q->orWhere('a.norm', 'like', "%{$searchValue}%")
                    ->orWhere('t_daftarpasien.noregistrasi', 'like', "%{$searchValue}%")
                    ->orWhere('t_daftarpasien.statuspasien', 'like', "%{$searchValue}%")
                    ->orWhere('a.nik', 'like', "%{$searchValue}%")
                    ->orWhere('t_daftarpasien.noantrian', 'like', "%{$searchValue}%");
            });
        }
        $query->where('t_daftarpasien.status', '!=', 'Dalam Antrian');
        $query->where('t_daftarpasien.status', '!=', 'Dibatalkan');
        if ($status && $status != 'All') {
            if ($status == "Not Yet") {
                $query->where('t_daftarpasien.status', '!=', 'Selesai');
            } else {
                $query->where('t_daftarpasien.status', $status);
            }
        }

        // Total record sebelum filter
        $totalData = DaftarPasien::count();

        // Total setelah filter
        $filteredData = $query->count();

        // Sorting dan pagination
        $query->orderBy($orderColumnName, $orderDirection)
            ->skip($offset)
            ->take($limit);

        $data = $query->get();

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $filteredData,
            'data' => $data,
        ]);
    }
}
