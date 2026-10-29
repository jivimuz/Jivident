<?php

namespace App\Http\Controllers\Website;

use App\Helpers\AppointmentHelper;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\RuangPraktek;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class AppointmentController extends Controller
{
    public function index()
    {
        return view('pages/booking/index');
    }

    public function getDokterList(Request $request)
    {
        $q = $request->params;

        $data = User::select(['m_users.id', 'm_employee.fullname', 'm_employee.nik'])
            ->join('m_employee', 'm_employee.id', '=', 'm_users.fk_employee')
            ->where('m_employee.is_pemeriksapasien', 1)
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
        $data = RuangPraktek::where('fk_dokter', $dokterId)->get(['id', 'name']);

        return response()->json(['message' => 'success', 'data' => $data], 200);
    }

    public function getPatientList(Request $request)
    {
        $q = $request->params;

        $data = Patient::where('klienfk', Auth::user()->klienfk ?? 1)
            ->when($q, function ($query) use ($q) {
                $query->where('nama', 'like', "%{$q}%")->orWhere('norm', 'like', "%{$q}%");
            })
            ->limit(20)
            ->get(['id', 'norm', 'nama']);

        return response()->json(['message' => 'success', 'data' => $data], 200);
    }

    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'fk_dokter' => 'required|integer',
            'fk_ruangpraktek' => 'required|integer',
            'tanggal' => 'required|date',
        ]);

        $slots = app(AppointmentHelper::class)->getAvailableSlots(
            (int) $request->fk_dokter,
            (int) $request->fk_ruangpraktek,
            $request->tanggal
        );

        return response()->json(['message' => 'success', 'data' => $slots], 200);
    }

    public function store(Request $request)
    {
        try {
            $appointment = app(AppointmentHelper::class)->book($request->only([
                'fk_dokter', 'fk_ruangpraktek', 'fk_pasien', 'tanggal', 'jam_mulai', 'notes',
            ]));

            return response()->json(['message' => 'success', 'data' => $appointment], 200);
        } catch (Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function cancel($id)
    {
        // AppointmentHelper::cancel() sendiri tidak men-scope ke klienfk (lihat
        // catatan di AppointmentHelper), jadi cek kepemilikan cabang dilakukan
        // di sini dulu — tanpa ini staf dari cabang lain bisa membatalkan
        // appointment milik cabang yang bukan miliknya lewat ID.
        if (! Appointment::where('id', $id)->where('klienfk', Auth::user()->klienfk ?? 1)->exists()) {
            return response()->json(['error' => 'Appointment tidak ditemukan'], 404);
        }

        try {
            app(AppointmentHelper::class)->cancel((int) $id);

            return response()->json(['message' => 'success'], 200);
        } catch (Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getList(Request $request)
    {
        $query = Appointment::with(['dokter.employee:id,fullname', 'pasien:id,nama', 'ruangPraktek:id,name'])
            ->where('klienfk', Auth::user()->klienfk ?? 1);

        if ($request->tanggal) {
            $query->where('tanggal', $request->tanggal);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $recordsTotal = $query->count();

        $data = $query->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->skip((int) $request->input('start', 0))
            ->take((int) $request->input('length', 10))
            ->get()
            ->map(function ($row) {
                return [
                    'id' => $row->id,
                    'tanggal' => $row->tanggal->format('Y-m-d'),
                    'jam' => substr($row->jam_mulai, 0, 5).' - '.substr($row->jam_selesai, 0, 5),
                    'dokter' => $row->dokter->employee->fullname ?? '-',
                    'ruang_praktek' => $row->ruangPraktek->name ?? '-',
                    'pasien' => $row->pasien->nama ?? '-',
                    'status' => $row->status,
                    'notes' => $row->notes,
                ];
            });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data,
        ]);
    }
}
