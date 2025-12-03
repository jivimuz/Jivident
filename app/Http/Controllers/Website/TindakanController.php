<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Tindakan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TindakanController extends Controller
{
    public function index()
    {
        return view('pages/tindakan/index');
    }

    public function getList()
    {
        $data = Tindakan::where('klienfk', Auth::user()->klienfk ?? 1)->get();
        return response()->json(["message" => 'success', 'data' => $data], 200);
    }

    // SHOW BY ID (for Edit Modal)
    public function showTindakan(Request $request)
    {
        $id = $request->id;
        $data = Tindakan::find($id);
        return response()->json($data);
    }

    // CREATE / UPDATE
    public function saveTindakan(Request $request)
    {
        $request->validate([
            'name'   => 'required|string',
            'amount' => 'required|numeric|min:0|max:9999999999'
        ]);

        // Jika ada ID = update
        if ($request->id) {
            $t = Tindakan::find($request->id);
        } else {
            $t = new Tindakan();
        }

        $t->name = $request->name;
        $t->amount = $request->amount;
        $t->save();

        return response()->json(['status' => 'success']);
    }

    // DELETE
    public function deleteTindakan(Request $request)
    {
        Tindakan::where('id', $request->id)->delete();
        return response()->json(['status' => 'deleted']);
    }

    public function getTindakanList(Request $request)
    {
        $q = $request->q;

        $data = Tindakan::select(['id', 'name', 'amount'])->where('klienfk', Auth::user()->klienfk)
            ->when($q, function ($query) use ($q) {
                $query->where('nama_tindakan', 'LIKE', "%{$q}%");
            })
            ->limit(20)
            ->get();

        return response()->json([
            "message" => "success",
            "data" => $data
        ], 200);
    }


    public function getDokterList(Request $request)
    {
        $q = $request->q;

        $data = Employee::select([
            'a.id',
            'a.display_name as name',
        ])
            ->join('m_users as a', 'a.fk_employee', 'm_employee.id')
            ->where('is_pemeriksapasien', true)
            ->where('klienfk', Auth::user()->klienfk)
            ->when($q, function ($query) use ($q) {
                $query->where('a.display_name', 'LIKE', "%{$q}%");
            })
            ->limit(20)
            ->get();

        return response()->json([
            "message" => 'success',
            "data" => $data
        ], 200);
    }
}
