<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\RuanganOperasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Master CRUD for operating rooms. Unlike `m_kamar` (Rawat Inap), a room
 * here isn't split into several beds — one room serves one case at a time,
 * so there's no child "bed" table. "Currently in use" is derived live from
 * `t_jadwal_operasi` (status='berlangsung') by JadwalOperasiController, not
 * stored on the room — `status` here only flags a room active/maintenance.
 */
class RuanganOperasiController extends Controller
{
    public function index()
    {
        return view('pages/ruangan_operasi/index');
    }

    public function getList(Request $request)
    {
        $klienfk = Auth::user()->klienfk ?? 1;

        $query = RuanganOperasi::where('klienfk', $klienfk)
            ->withCount(['jadwal as jadwal_aktif_count' => function ($q) {
                $q->whereIn('status', ['dijadwalkan', 'berlangsung']);
            }]);
        $total = (clone $query)->count();
        if ($search = $request->input('search.value')) {
            $query->where(function ($builder) use ($search) {
                $builder->where('nama', 'like', "%{$search}%")
                    ->orWhere('kode', 'like', "%{$search}%");
            });
        }
        $length = min(max((int) $request->input('length', 10), 1), 100);
        $page = $query->orderBy('nama')->paginate($length, ['*'], 'page', (int) floor((int) $request->input('start', 0) / $length) + 1);

        return response()->json(['draw' => (int) $request->input('draw'), 'recordsTotal' => $total, 'recordsFiltered' => $page->total(), 'data' => $page->items()], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'kode' => 'nullable|string|max:50',
            'lantai' => 'nullable|string|max:50',
            'status' => 'required|in:tersedia,maintenance',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        $ruangan = RuanganOperasi::create(array_merge($validated, [
            'klienfk' => Auth::user()->klienfk ?? 1,
        ]));

        return response()->json(['message' => 'success', 'data' => $ruangan], 201);
    }

    public function update(Request $request, $id)
    {
        $ruangan = RuanganOperasi::where('klienfk', Auth::user()->klienfk ?? 1)->find($id);
        if (! $ruangan) {
            return response()->json(['message' => 'error', 'error' => 'Ruangan tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'kode' => 'nullable|string|max:50',
            'lantai' => 'nullable|string|max:50',
            'status' => 'required|in:tersedia,maintenance',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        $ruangan->update($validated);

        return response()->json(['message' => 'success', 'data' => $ruangan], 200);
    }

    public function destroy($id)
    {
        $ruangan = RuanganOperasi::where('klienfk', Auth::user()->klienfk ?? 1)
            ->withCount(['jadwal as jadwal_aktif_count' => function ($q) {
                $q->whereIn('status', ['dijadwalkan', 'berlangsung']);
            }])
            ->find($id);

        if (! $ruangan) {
            return response()->json(['message' => 'error', 'error' => 'Ruangan tidak ditemukan'], 404);
        }

        if ($ruangan->jadwal_aktif_count > 0) {
            return response()->json([
                'message' => 'error',
                'error' => 'Ruangan masih punya jadwal operasi aktif, tidak bisa dihapus',
            ], 400);
        }

        $ruangan->delete();

        return response()->json(['message' => 'success'], 200);
    }
}
