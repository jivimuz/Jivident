<?php

namespace App\Http\Controllers\Website;

use App\Helpers\SuratMandiriHelper;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Payments;
use App\Models\Setting;
use App\Models\SuratMandiri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class SuratMandiriController extends Controller
{
    public function index()
    {
        $dokters = Employee::where('is_pemeriksapasien', 1)->orderBy('fullname')->get(['id', 'fullname']);
        $payments = Payments::orderBy('title')->get(['id', 'title']);
        $harga = (float) (Setting::where('parameter', 'patient_surat_keterangan_price')->value('value') ?? 0);

        return view('pages/surat-mandiri/index', compact('dokters', 'payments', 'harga'));
    }

    public function getList(Request $request)
    {
        $helper = app(SuratMandiriHelper::class);

        $query = SuratMandiri::with(['dokter:id,fullname', 'payment:id,title'])
            ->where('klienfk', Auth::user()->klienfk ?? 1);

        $recordsTotal = $query->count();

        $data = $query->orderByDesc('id')
            ->skip((int) $request->input('start', 0))
            ->take((int) $request->input('length', 10))
            ->get()
            ->map(function ($row) use ($helper) {
                return [
                    'id' => $row->id,
                    'created_at' => $row->created_at->format('d/m/Y H:i'),
                    'jenis' => $helper->jenisLabel($row->jenis),
                    'nama' => $row->nama,
                    'dokter' => $row->dokter->fullname ?? '-',
                    'payment' => $row->payment->title ?? '-',
                    'harga' => $row->harga,
                ];
            });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis' => 'required|in:sehat,sakit,rujukan,umum',
            'nama' => 'required|string|max:150',
            'jenis_kelamin' => 'nullable|string|max:20',
            'umur' => 'nullable|string|max:30',
            'alamat' => 'nullable|string|max:255',
            'pekerjaan' => 'nullable|string|max:100',
            'keperluan' => 'nullable|string|max:150',
            'fk_employee' => 'required|integer',
            'fk_payment' => 'required|integer',
            'detail' => 'nullable|array',
        ]);

        try {
            $surat = app(SuratMandiriHelper::class)->issue($validated);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'success',
            'id' => $surat->id,
            'harga' => $surat->harga,
        ], 200);
    }
}
