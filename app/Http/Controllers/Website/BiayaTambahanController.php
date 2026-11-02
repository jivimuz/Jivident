<?php

namespace App\Http\Controllers\Website;

use App\Helpers\BillingStatusHelper;
use App\Http\Controllers\Controller;
use App\Models\BiayaTambahan;
use App\Models\DaftarPasien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Admin-only entry point for charging a patient's visit with something that
 * doesn't come from tindakan/diagnosa/lab/radiologi/resep's own catalogs —
 * "Biaya Operasi" was the motivating example, but this is deliberately
 * generic (free-text `nama`), not Jadwal-Operasi-specific. Unlike `KSR`
 * (TransactionController's manual cashier entry), every charge here is tied
 * to `daftarpasien_id` and feeds the same outstanding-balance calculation
 * as the clinical charge types, so it shows up on that visit's tagihan and
 * is paid through the normal Billing flow.
 */
class BiayaTambahanController extends Controller
{
    public function index()
    {
        return view('pages/biaya_tambahan/index');
    }

    public function getList(Request $request)
    {
        $klienfk = Auth::user()->klienfk ?? 1;
        $searchValue = $request->input('search.value');
        $limit = (int) $request->input('length', 10);
        $start = (int) $request->input('start', 0);

        $query = BiayaTambahan::query()
            ->select([
                't_biaya_tambahan.*',
                'p.nama as pasien_nama',
                'p.norm as pasien_norm',
                'dp.noregistrasi',
                'u.display_name as dibuat_oleh',
            ])
            ->join('t_daftarpasien as dp', 'dp.id', 't_biaya_tambahan.daftarpasien_id')
            ->join('m_pasien as p', 'p.id', 'dp.pasienfk')
            ->leftJoin('m_users as u', 'u.id', 't_biaya_tambahan.created_by')
            ->where('t_biaya_tambahan.klienfk', $klienfk);

        $totalData = (clone $query)->count();

        if (! empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('p.nama', 'like', "%{$searchValue}%")
                    ->orWhere('p.norm', 'like', "%{$searchValue}%")
                    ->orWhere('t_biaya_tambahan.nama', 'like', "%{$searchValue}%")
                    ->orWhere('dp.noregistrasi', 'like', "%{$searchValue}%");
            });
        }
        $totalFiltered = $query->count();

        $data = $query->orderByDesc('t_biaya_tambahan.created_at')
            ->offset($start)
            ->limit($limit > 0 ? $limit : 10)
            ->get();

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ]);
    }

    /**
     * Visit picker for the "add charge" form — searches active (not
     * cancelled) visits by No. RM/nama/no. registrasi, most recent first,
     * same shape as the patient/visit pickers used elsewhere in this app
     * (Appointment/IGD's own getPatientList-style endpoints).
     */
    public function getDaftarPasienList(Request $request)
    {
        $klienfk = Auth::user()->klienfk ?? 1;
        $q = $request->params;

        $data = DaftarPasien::query()
            ->select(['t_daftarpasien.id', 't_daftarpasien.noregistrasi', 't_daftarpasien.tglregistrasi', 'p.nama', 'p.norm'])
            ->join('m_pasien as p', 'p.id', 't_daftarpasien.pasienfk')
            ->where('t_daftarpasien.klienfk', $klienfk)
            ->where('t_daftarpasien.status', '!=', 'Dibatalkan')
            ->when($q, function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('p.nama', 'like', "%{$q}%")
                        ->orWhere('p.norm', 'like', "%{$q}%")
                        ->orWhere('t_daftarpasien.noregistrasi', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('t_daftarpasien.tglregistrasi')
            ->limit(20)
            ->get();

        return response()->json(['message' => 'success', 'data' => $data], 200);
    }

    public function store(Request $request)
    {
        $klienfk = Auth::user()->klienfk ?? 1;

        $validated = $request->validate([
            'daftarpasien_id' => 'required|integer',
            'nama' => 'required|string|max:255',
            'jumlah' => 'required|numeric|min:1',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        $daftarPasien = DaftarPasien::where('id', $validated['daftarpasien_id'])->where('klienfk', $klienfk)->first();
        if (! $daftarPasien) {
            return response()->json(['message' => 'error', 'error' => 'Kunjungan tidak ditemukan'], 404);
        }

        $biaya = DB::transaction(function () use ($validated, $klienfk, $daftarPasien) {
            $row = BiayaTambahan::create([
                'klienfk' => $klienfk,
                'daftarpasien_id' => $daftarPasien->id,
                'nama' => $validated['nama'],
                'jumlah' => $validated['jumlah'],
                'keterangan' => $validated['keterangan'] ?? null,
                'created_by' => Auth::id(),
            ]);

            app(BillingStatusHelper::class)->refresh($daftarPasien->id);

            return $row;
        });

        return response()->json(['message' => 'success', 'data' => $biaya], 201);
    }

    public function destroy($id)
    {
        $klienfk = Auth::user()->klienfk ?? 1;

        $biaya = BiayaTambahan::where('id', $id)->where('klienfk', $klienfk)->first();
        if (! $biaya) {
            return response()->json(['message' => 'error', 'error' => 'Data tidak ditemukan'], 404);
        }

        $daftarPasien = DaftarPasien::find($biaya->daftarpasien_id);
        if ($daftarPasien && $daftarPasien->statuspembayaran === 'Lunas') {
            return response()->json([
                'message' => 'error',
                'error' => 'Tagihan kunjungan ini sudah lunas & ditutup, tidak bisa menghapus biaya lagi',
            ], 400);
        }

        $daftarpasienId = $biaya->daftarpasien_id;
        $biaya->delete();

        app(BillingStatusHelper::class)->refresh($daftarpasienId);

        return response()->json(['message' => 'success'], 200);
    }
}
