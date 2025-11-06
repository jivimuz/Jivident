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
        $orderColumnIndex = $request->input('order.1.column', 0);
        $orderDirection = $request->input('order.1.dir', 'desc'); // default desc 👈

        $columns = [
            '*',
        ];
        $orderColumnName = $columns[$orderColumnIndex] ?? 'm_agents.id';

        $query = Patient::query()
            ->select(
                '*',


            );

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('*', 'like', "%{$searchValue}%");
            });
        }

        $totalData = $query->count();

        // Urutan default DESC by id
        $query->orderBy($orderColumnName, $orderDirection ?? 'desc');

        $data = $query->skip($offset)->take($limit)->get();

        return response()->json([
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalData),
            "data" => $data,
        ]);
    }
}
