<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\JadwalDokter;
use App\Models\RuangPraktek;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalDokterController extends Controller
{
    private const HARI_LABEL = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    public function index()
    {
        return view('pages/jadwal-dokter/index');
    }

    public function getDokterList(Request $request)
    {
        $q = $request->params;

        $data = User::select(['m_users.id', 'm_employee.fullname', 'm_employee.nik'])
            ->join('m_employee', 'm_employee.id', '=', 'm_users.fk_employee')
            ->where('m_employee.is_pemeriksapasien', 1)
            ->where('m_users.klienfk', Auth::user()->klienfk ?? 1)
            ->when($q, function ($query) use ($q) {
                $query->where('m_employee.fullname', 'like', "%{$q}%")
                    ->orWhere('m_employee.nik', 'like', "%{$q}%");
            })
            ->limit(20)
            ->get();

        return response()->json(['message' => 'success', 'data' => $data], 200);
    }

    public function getRuangPraktekByDokter($dokterId)
    {
        // Not scoped by m_ruangpraktek.fk_dokter: that column is the legacy
        // single-doctor-per-room assignment, which would hide a room from
        // every doctor except the one already "owning" it. A room can now
        // be shared by several doctors across different JadwalDokter shifts,
        // so any active room in the branch is a valid pick for any doctor.
        $data = RuangPraktek::where('klienfk', Auth::user()->klienfk ?? 1)->get(['id', 'name']);

        return response()->json(['message' => 'success', 'data' => $data], 200);
    }

    public function getList(Request $request)
    {
        $query = JadwalDokter::with(['dokter:id', 'dokter.employee:id,fullname', 'ruangPraktek:id,name'])
            ->where('klienfk', Auth::user()->klienfk ?? 1);

        $recordsTotal = $query->count();

        $data = $query->orderBy('fk_dokter')
            ->orderBy('hari')
            ->skip((int) $request->input('start', 0))
            ->take((int) $request->input('length', 10))
            ->get()
            ->map(function ($row) {
                return [
                    'id' => $row->id,
                    'dokter' => $row->dokter->employee->fullname ?? '-',
                    'ruang_praktek' => $row->ruangPraktek->name ?? '-',
                    'hari' => self::HARI_LABEL[$row->hari],
                    'jam' => substr($row->jam_mulai, 0, 5).' - '.substr($row->jam_selesai, 0, 5),
                    'durasi_slot' => $row->durasi_slot,
                    'is_active' => (bool) $row->is_active,
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
        $validated = $this->validateRequest($request);

        if ($conflict = $this->findRoomConflict($validated)) {
            return response()->json([
                'error' => "Ruangan ini sudah dipakai {$conflict} pada jam tersebut.",
                'message' => "Ruangan ini sudah dipakai {$conflict} pada jam tersebut.",
            ], 400);
        }

        JadwalDokter::create(array_merge($validated, [
            'klienfk' => Auth::user()->klienfk ?? 1,
        ]));

        return response()->json(['message' => 'success'], 200);
    }

    public function update(Request $request, $id)
    {
        $validated = $this->validateRequest($request);

        if ($conflict = $this->findRoomConflict($validated, (int) $id)) {
            return response()->json([
                'error' => "Ruangan ini sudah dipakai {$conflict} pada jam tersebut.",
                'message' => "Ruangan ini sudah dipakai {$conflict} pada jam tersebut.",
            ], 400);
        }

        JadwalDokter::where('klienfk', Auth::user()->klienfk ?? 1)->findOrFail($id)->update($validated);

        return response()->json(['message' => 'success'], 200);
    }

    /**
     * A room can be shared by several doctors, but not at the literal same
     * day+time — guards against a data-entry mistake double-booking the
     * physical room itself. Returns the conflicting doctor's name, or null.
     */
    private function findRoomConflict(array $validated, ?int $ignoreId = null): ?string
    {
        $conflict = JadwalDokter::with('dokter.employee')
            ->where('klienfk', Auth::user()->klienfk ?? 1)
            ->where('fk_ruangpraktek', $validated['fk_ruangpraktek'])
            ->where('hari', $validated['hari'])
            ->where('fk_dokter', '!=', $validated['fk_dokter'])
            ->where('is_active', true)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('jam_mulai', '<', $validated['jam_selesai'])
            ->where('jam_selesai', '>', $validated['jam_mulai'])
            ->first();

        return $conflict?->dokter?->employee?->fullname ?? ($conflict ? 'dokter lain' : null);
    }

    public function destroy($id)
    {
        JadwalDokter::where('klienfk', Auth::user()->klienfk ?? 1)->findOrFail($id)->delete();

        return response()->json(['message' => 'success'], 200);
    }

    private function validateRequest(Request $request): array
    {
        $validated = $request->validate([
            'fk_dokter' => 'required|integer',
            'fk_ruangpraktek' => 'required|integer',
            'hari' => 'required|integer|min:0|max:6',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'durasi_slot' => 'required|integer|min:5',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        return $validated;
    }
}
