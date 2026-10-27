<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\JadwalKerja;
use App\Models\JadwalKerjaKaryawan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class JadwalKerjaController extends Controller
{
    public function index()
    {
        return view('pages/jadwal-kerja/index');
    }

    public function getTemplates()
    {
        return response()->json(['message' => 'success', 'data' => JadwalKerja::orderBy('kode')->get()], 200);
    }

    public function storeTemplate(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:50',
            'nama' => 'required|string|max:255',
            'jammasuk' => 'required',
            'jampulang' => 'required',
        ]);

        JadwalKerja::create($request->only(['kode', 'nama', 'jammasuk', 'jampulang']));

        return response()->json(['message' => 'success'], 200);
    }

    public function destroyTemplate($id)
    {
        if (JadwalKerjaKaryawan::where('fk_jadwalkerja', $id)->exists()) {
            return response()->json(['error' => 'Template shift ini masih dipakai di jadwal karyawan'], 400);
        }

        JadwalKerja::findOrFail($id)->delete();

        return response()->json(['message' => 'success'], 200);
    }

    public function getEmployeeList(Request $request)
    {
        $q = $request->q;

        $data = Employee::when($q, function ($query) use ($q) {
            $query->where('fullname', 'like', "%{$q}%")->orWhere('nik', 'like', "%{$q}%");
        })->limit(20)->get(['id', 'nik', 'fullname']);

        return response()->json(['message' => 'success', 'data' => $data], 200);
    }

    public function getList(Request $request)
    {
        $query = JadwalKerjaKaryawan::with(['employee:id,fullname', 'template:id,kode,nama']);

        if ($request->fk_employee) {
            $query->where('fk_employee', $request->fk_employee);
        }
        if ($request->start_date) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        $recordsTotal = $query->count();

        $data = $query->orderBy('tanggal', 'desc')
            ->skip((int) $request->input('start', 0))
            ->take((int) $request->input('length', 10))
            ->get()
            ->map(function ($row) {
                return [
                    'id' => $row->id,
                    'employee' => $row->employee->fullname ?? '-',
                    'tanggal' => $row->tanggal->format('Y-m-d'),
                    'template' => $row->template->nama ?? 'Custom',
                    'jam_masuk' => $row->jam_masuk->format('Y-m-d H:i'),
                    'jam_pulang' => $row->jam_pulang->format('Y-m-d H:i'),
                    'lintas_hari' => $row->jam_masuk->format('Y-m-d') !== $row->jam_pulang->format('Y-m-d'),
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
        $request->validate([
            'fk_employee' => 'required|integer',
            'tanggal' => 'required|date',
        ]);

        [$jamMasuk, $jamPulang] = $this->resolveShiftTimes($request);

        $entry = JadwalKerjaKaryawan::create([
            'fk_employee' => $request->fk_employee,
            'fk_jadwalkerja' => $request->fk_jadwalkerja ?: null,
            'tanggal' => $request->tanggal,
            'jam_masuk' => $jamMasuk,
            'jam_pulang' => $jamPulang,
            'notes' => $request->notes,
        ]);

        return response()->json(['message' => 'success', 'data' => $entry], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'fk_employee' => 'required|integer',
            'tanggal' => 'required|date',
        ]);

        [$jamMasuk, $jamPulang] = $this->resolveShiftTimes($request);

        JadwalKerjaKaryawan::findOrFail($id)->update([
            'fk_employee' => $request->fk_employee,
            'fk_jadwalkerja' => $request->fk_jadwalkerja ?: null,
            'tanggal' => $request->tanggal,
            'jam_masuk' => $jamMasuk,
            'jam_pulang' => $jamPulang,
            'notes' => $request->notes,
        ]);

        return response()->json(['message' => 'success'], 200);
    }

    public function destroy($id)
    {
        JadwalKerjaKaryawan::findOrFail($id)->delete();

        return response()->json(['message' => 'success'], 200);
    }

    /**
     * Hitung jam_masuk/jam_pulang sebagai DATETIME penuh. Kalau dari template dan
     * jam pulang <= jam masuk, atau kalau custom dan jam_pulang_time <= jam_masuk_time,
     * otomatis dianggap shift lintas hari (pulang di tanggal besoknya) — tanpa perlu
     * checkbox "lintas hari" terpisah, karena perbedaan tanggalnya sendiri yang mewakili itu.
     */
    private function resolveShiftTimes(Request $request): array
    {
        $tanggal = Carbon::parse($request->tanggal)->startOfDay();

        if ($request->fk_jadwalkerja) {
            $template = JadwalKerja::findOrFail($request->fk_jadwalkerja);
            $jamMasukTime = $template->jammasuk;
            $jamPulangTime = $template->jampulang;
        } else {
            $request->validate([
                'jam_masuk_time' => 'required',
                'jam_pulang_time' => 'required',
            ]);
            $jamMasukTime = $request->jam_masuk_time;
            $jamPulangTime = $request->jam_pulang_time;
        }

        $jamMasuk = $tanggal->copy()->setTimeFromTimeString($jamMasukTime);
        $jamPulang = $tanggal->copy()->setTimeFromTimeString($jamPulangTime);

        if ($jamPulang->lessThanOrEqualTo($jamMasuk)) {
            $jamPulang->addDay();
        }

        return [$jamMasuk, $jamPulang];
    }
}
