<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\RawatInap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * "Daftar Rawat Inap" under the Daftar Pasien sidebar group — a read list of
 * who's currently (or previously) admitted, mirroring the Ruang Tunggu/Sudah
 * Diperiksa/Dibatalkan list pages in that same group. Admission/discharge/
 * room management stays on the dedicated /rawat-inap page (module RWI) and
 * mid-stay room transfers stay on the Pemeriksaan page's Rawat Inap tab —
 * this page only lists patients and jumps into Pemeriksaan per row, same
 * "Periksa" link convention as its sibling list pages.
 */
class PasienRawatInapController extends Controller
{
    public function index()
    {
        return view('pages/pasien_rawat_inap/index');
    }

    public function getList(Request $request)
    {
        $klienfk = Auth::user()->klienfk ?? 1;
        $offset = $request->input('start', 0);
        $limit = $request->input('length', 10);
        $searchValue = $request->post('pasienSearch');
        $status = $request->input('status', 'dirawat');

        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderDirection = $request->input('order.0.dir', 'desc');

        $columns = [
            't_rawatinap.tgl_masuk',
            'm_pasien.norm',
            'm_pasien.nama',
            'm_kamar.nama as kamar_nama',
            'm_employee.fullname as dokter_nama',
            't_rawatinap.status',
            't_rawatinap.fk_daftarpasien',
            't_rawatinap.id',
        ];

        $orderColumnName = $columns[$orderColumnIndex] ?? 't_rawatinap.tgl_masuk';

        $query = RawatInap::query()->select($columns);
        $query->join('m_pasien', 'm_pasien.id', 't_rawatinap.fk_pasien');
        $query->join('m_kamar', 'm_kamar.id', 't_rawatinap.fk_kamar');
        $query->leftJoin('m_employee', 'm_employee.id', 't_rawatinap.fk_dokter_penanggungjawab');
        $query->where('t_rawatinap.klienfk', $klienfk);
        $query->where('t_rawatinap.status', $status);

        if ($searchValue) {
            $query->where(function ($q) use ($searchValue) {
                $q->orWhere('m_pasien.norm', 'like', "%{$searchValue}%")
                    ->orWhere('m_pasien.nama', 'like', "%{$searchValue}%");
            });
        }

        $totalData = RawatInap::where('klienfk', $klienfk)->count();
        $filteredData = $query->count();

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
