<?php

namespace App\Http\Controllers\Website;

use App\Helpers\BpjsClaimHelper;
use App\Http\Controllers\Controller;
use App\Models\BpjsClaim;
use App\Models\DaftarPasien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BpjsClaimController extends Controller
{
    public function index()
    {
        return view('pages/bpjs-claims/index');
    }

    public function getList(Request $request)
    {
        $query = BpjsClaim::query()
            ->with(['visit:id,noregistrasi', 'patient:id,norm,nama,nobpjs'])
            ->where('klienfk', Auth::user()->klienfk)
            ->latest('id');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($builder) use ($search) {
                $builder->where('sep_no', 'like', "%{$search}%")
                    ->orWhere('claim_no', 'like', "%{$search}%")
                    ->orWhereHas('patient', function ($patient) use ($search) {
                        $patient->where('nama', 'like', "%{$search}%")
                            ->orWhere('norm', 'like', "%{$search}%")
                            ->orWhere('nobpjs', 'like', "%{$search}%");
                    });
            });
        }

        return response()->json(['data' => $query->paginate(20)]);
    }

    public function store(Request $request)
    {
        $klienfk = Auth::user()->klienfk;
        $data = $request->validate([
            'daftarpasien_id' => 'required|integer',
            'sep_no' => 'nullable|string|max:40',
            'claim_no' => 'nullable|string|max:40',
            'amount' => 'required|numeric|min:0',
            'service_date' => 'nullable|date',
            'notes' => 'nullable|string|max:5000',
        ]);

        $visit = DaftarPasien::where('id', $data['daftarpasien_id'])
            ->where('klienfk', $klienfk)
            ->where('jenispembayaran', 'BPJS')
            ->first();
        if (! $visit) {
            return response()->json(['message' => 'Kunjungan BPJS tidak ditemukan.'], 404);
        }
        if (BpjsClaim::where('klienfk', $klienfk)->where('daftarpasien_id', $visit->id)->exists()) {
            return response()->json(['message' => 'Kunjungan ini sudah memiliki klaim.'], 422);
        }

        $claim = BpjsClaimHelper::create($data + [
            'pasien_id' => $visit->pasienfk,
            'service_date' => $data['service_date'] ?? date('Y-m-d', strtotime($visit->tglregistrasi)),
        ], $klienfk, Auth::id());

        return response()->json(['message' => 'Klaim BPJS berhasil dibuat.', 'data' => $claim], 201);
    }

    public function transition(Request $request, int $id)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(BpjsClaimHelper::STATUSES)],
            'claim_no' => 'nullable|string|max:40',
            'notes' => 'nullable|string|max:5000',
            'response_payload' => 'nullable|array',
        ]);
        $claim = BpjsClaim::where('id', $id)->where('klienfk', Auth::user()->klienfk)->firstOrFail();

        $claim = BpjsClaimHelper::transition($claim, $data['status'], Auth::id(), $data);

        return response()->json(['message' => 'Status klaim diperbarui.', 'data' => $claim]);
    }
}
