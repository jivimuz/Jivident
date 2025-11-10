<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Odontogram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OdontogramController extends Controller
{
    public function index($id)
    {
        $odontogram = Odontogram::where('fk_daftarpasien', base64_decode(base64_decode($id)))->first();
        return view('pages/pemeriksaan/content/odontogram', compact('odontogram', 'id'));
    }

    public function field(Request $request, $id)
    {
        try {

            // update jika ada id, kalau tidak maka insert baru
            Odontogram::updateOrCreate(
                ['fk_daftarpasien' => base64_decode(base64_decode($id))],
                [
                    'klienfk' => Auth::user()->klienfk ?? 1,
                    'typeodontogram' => $request->odontotype ?? 1,
                ]
            );
            $field = null;
            switch ($request->odontotype) {
                case '3D Anak (20 Gigi)':
                    $field = '3D_anak';
                    break;
                case '3D Remaja (24 Gigi)':
                    $field = '3D_remaja';
                    break;
                case '3D Dewasa (32 Gigi)':
                    $field = '3D_dewasa';
                    break;
                case '2D Global':
                    $field = '2d_global';
                    break;
                default:
                    $field = null;
                    break;
            }

            if (!$field) {
                return 'Pilih type terlebih dahulu';
            }
            return view('pages/pemeriksaan/content/odontogram-field/' . $field);
        } catch (\Throwable $e) {
            return response()->json([
                'code' => 0,
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
