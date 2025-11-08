<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatientController extends Controller
{
    public function index()
    {
        return view('pages/patient/index');
    }

    public function getList(Request $request)
    {
        $offset = $request->input('start', 0);
        $limit = $request->input('length', 10);
        $searchValue = $request->input('search.value', '');
        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderDirection = $request->input('order.0.dir', 'desc');
        // $nik = $request->post('nik_search');

        $columns = [
            'id',
            'nama',
            'norm',
            'nik',
            'nobpjs',
            'nohp',
            'tgllahir',
        ];

        $orderColumnName = $columns[$orderColumnIndex] ?? 'id';

        // Base query
        $query = Patient::query()->select($columns);

        // Filter by NIK (kalau ada)
        // if ($nik) {
        //     $query->where('nik', $nik);
        // }

        // Search global
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($columns, $searchValue) {
                foreach ($columns as $col) {
                    $q->orWhere($col, 'like', "%{$searchValue}%");
                }
            });
        }

        // Total record sebelum filter
        $totalData = Patient::count();

        // Total setelah filter
        $filteredData = $query->count();

        // Sorting dan pagination
        $query->orderBy($orderColumnName, $orderDirection)
            ->skip($offset)
            ->take($limit);

        $data = $query->get();

        // Convert tanggal ke WIB
        $data->transform(function ($item) {
            if ($item->tgllahir) {
                $item->tgllahir = \Carbon\Carbon::parse($item->tgllahir)
                    ->setTimezone('Asia/Jakarta')
                    ->translatedFormat('d M Y H:i'); // pakai bahasa Indo otomatis
            }
            return $item;
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $filteredData,
            'data' => $data,
        ]);
    }
}
