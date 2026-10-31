<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\DaftarPasien;
use App\Models\Odontogram;
use App\Models\OdontogramDiagnosis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OdontogramController extends Controller
{
    public function index($id)
    {
        $status = DaftarPasien::select('status')->where('id', base64_decode(base64_decode($id)))->first();
        $odontogram = Odontogram::where('fk_daftarpasien', base64_decode(base64_decode($id)))->first();
        return view('pages/pemeriksaan/content/odontogram', compact('odontogram', 'id', 'status'));
    }

    public function field(Request $request, $id)
    {
        $status = DaftarPasien::select('status')->where('id', base64_decode(base64_decode($id)))->first();
        $odontotype = $request->odontotype ?? '-';
        // update jika ada id, kalau tidak maka insert baru

        switch ($odontotype) {
            case '3D Anak (20 Gigi)':
                $field = '3D_anak';
                break;

            case '3D Dewasa (32 Gigi)':
                $field = '3D_dewasa';
                break;
            case '2D Global':
                $field = '2D_global';
                break;
            default:
                $field = null;
                break;
        }

        if (!$field) {
            return 'Pilih type terlebih dahulu';
        }
        return view('pages/pemeriksaan/content/odontogram-field/' . $field, compact('id', 'odontotype', 'status'));
    }

    /**
     * Diagnosis/kondisi-gigi options for the odontogram pickers — sourced
     * from the master table (`m_odontogram_diagnosis`) instead of the old
     * hardcoded lists in adult.js/child.js/2dglobal.js. `kategori` picks
     * which picker is asking: '3d' for the Diagnostic Panel dropdown on the
     * 3D models, '2d' for the kondisi-gigi buttons on the 2D Global chart.
     */
    public function diagnosisList(Request $request)
    {
        $data = OdontogramDiagnosis::where('klienfk', Auth::user()->klienfk ?? 1)
            ->where('kategori', $request->kategori === '2d' ? '2d' : '3d')
            ->orderBy('urutan')
            ->orderBy('id')
            ->get(['id', 'nama', 'warna', 'simbol', 'is_reset']);

        return response()->json(['message' => 'success', 'data' => $data], 200);
    }

    public function saveOdonto(Request $request, $id)
    {
        try {
            $odontotype = $request->odontotype ?? '-';
            Odontogram::updateOrCreate(
                ['fk_daftarpasien' => base64_decode(base64_decode($id))],
                [
                    'klienfk' => Auth::user()->klienfk ?? 1,
                    'typeodontogram' => $odontotype,
                    'mappeddata' => $request->mapping,
                ]
            );
            return response()->json(['code' => 1, 'message' => 'Data berhasil disimpan'], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'code' => 0,
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ], 400);
        }
    }
    public function loadOdonto(Request $request, $id)
    {
        try {
            $odontotype = $request->odontotype ?? '-';
            $data = Odontogram::where('fk_daftarpasien', base64_decode(base64_decode($id)))->where('typeodontogram', $odontotype)->first();
            return response()->json(['code' => 1, 'message' => 'Data ditemukan', 'data' => $data], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'code' => 0,
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
