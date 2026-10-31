<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\OdontogramDiagnosis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Master CRUD for odontogram diagnosis/kondisi-gigi — one shared table
 * (`m_odontogram_diagnosis`) for both the 3D model's diagnosis dropdown and
 * the 2D Global chart's kondisi-gigi buttons, distinguished by `kategori`.
 * See OdontogramController::diagnosisList() for the read-only endpoint the
 * odontogram tabs themselves use to populate their picker from this table.
 */
class OdontogramDiagnosisController extends Controller
{
    public function index()
    {
        return view('pages/odontogram_diagnosis/index');
    }

    public function getList()
    {
        $data = OdontogramDiagnosis::where('klienfk', Auth::user()->klienfk ?? 1)
            ->orderBy('kategori')
            ->orderBy('urutan')
            ->orderBy('id')
            ->get();

        return response()->json(['message' => 'success', 'data' => $data], 200);
    }

    public function showDiagnosis(Request $request)
    {
        $data = OdontogramDiagnosis::where('klienfk', Auth::user()->klienfk ?? 1)->find($request->id);

        return response()->json($data);
    }

    public function saveDiagnosis(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'kategori' => 'required|in:3d,2d',
            'warna' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'simbol' => 'nullable|string|max:20',
            'urutan' => 'nullable|integer',
        ]);

        $klienfk = Auth::user()->klienfk ?? 1;

        if ($request->id) {
            $diagnosis = OdontogramDiagnosis::where('klienfk', $klienfk)->find($request->id);
            if (! $diagnosis) {
                return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
            }
        } else {
            $diagnosis = new OdontogramDiagnosis(['klienfk' => $klienfk]);
        }

        $diagnosis->nama = $request->nama;
        $diagnosis->kategori = $request->kategori;
        $diagnosis->warna = $request->warna;
        $diagnosis->simbol = $request->kategori === '2d' ? $request->simbol : null;
        $diagnosis->is_reset = $request->kategori === '2d' && $request->boolean('is_reset');
        $diagnosis->urutan = $request->urutan ?? 0;
        $diagnosis->save();

        return response()->json(['status' => 'success']);
    }

    public function deleteDiagnosis(Request $request)
    {
        OdontogramDiagnosis::where('id', $request->id)->where('klienfk', Auth::user()->klienfk ?? 1)->delete();

        return response()->json(['status' => 'deleted']);
    }
}
