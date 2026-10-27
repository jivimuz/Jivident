<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\Employee;
use App\Models\JenisCuti;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CutiController extends Controller
{
    public function index()
    {
        return view('pages/cuti/index');
    }

    public function getEmployeeList(Request $request)
    {
        $q = $request->q;

        $data = Employee::when($q, function ($query) use ($q) {
            $query->where('fullname', 'like', "%{$q}%")->orWhere('nik', 'like', "%{$q}%");
        })->limit(20)->get(['id', 'nik', 'fullname']);

        return response()->json(['message' => 'success', 'data' => $data], 200);
    }

    public function getJenisCutiList()
    {
        return response()->json(['message' => 'success', 'data' => JenisCuti::orderBy('name')->get()], 200);
    }

    public function getList(Request $request)
    {
        $query = Cuti::with(['employee:id,fullname', 'jenisCuti:id,name', 'approver:id,display_name']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $recordsTotal = $query->count();

        $data = $query->orderBy('id', 'desc')
            ->skip((int) $request->input('start', 0))
            ->take((int) $request->input('length', 10))
            ->get()
            ->map(function ($row) {
                return [
                    'id' => $row->id,
                    'nopermohonan' => $row->nopermohonan,
                    'employee' => $row->employee->fullname ?? '-',
                    'jenis_cuti' => $row->jenisCuti->name ?? '-',
                    'startdate' => $row->startdate->format('Y-m-d'),
                    'enddate' => $row->enddate->format('Y-m-d'),
                    'notes' => $row->notes,
                    'status' => $row->status,
                    'approver' => $row->approver->display_name ?? '-',
                    'rejected_reason' => $row->rejected_reason,
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
            'fk_employee' => 'required|integer|exists:m_employee,id',
            'fk_jeniscuti' => 'required|integer|exists:m_jeniscuti,id',
            'startdate' => 'required|date',
            'enddate' => 'required|date|after_or_equal:startdate',
        ]);

        $jenisCuti = JenisCuti::findOrFail($request->fk_jeniscuti);
        $now = Carbon::now();

        $cuti = Cuti::create([
            'kode' => 'CT-'.Str::upper(Str::random(6)),
            'fk_employee' => $request->fk_employee,
            'fk_jeniscuti' => $request->fk_jeniscuti,
            'tglpermohonan' => $now->toDateString(),
            'nopermohonan' => 'CUTI/'.$now->format('Ym').'/'.Str::upper(Str::random(5)),
            'tambah' => 0,
            'kurang' => 0,
            'saldo' => 0,
            'startdate' => $request->startdate,
            'enddate' => $request->enddate,
            'notes' => $request->notes,
            'alamat' => $request->alamat,
            'status' => 'pending',
            'issakit' => (bool) $jenisCuti->issakit,
        ]);

        return response()->json(['message' => 'success', 'data' => $cuti], 200);
    }

    public function approve($id)
    {
        $cuti = Cuti::findOrFail($id);
        if ($cuti->status !== 'pending') {
            return response()->json(['error' => 'Pengajuan ini sudah diproses sebelumnya'], 400);
        }

        $cuti->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return response()->json(['message' => 'success'], 200);
    }

    public function reject(Request $request, $id)
    {
        $cuti = Cuti::findOrFail($id);
        if ($cuti->status !== 'pending') {
            return response()->json(['error' => 'Pengajuan ini sudah diproses sebelumnya'], 400);
        }

        $cuti->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejected_reason' => $request->reason,
        ]);

        return response()->json(['message' => 'success'], 200);
    }

    public function destroy($id)
    {
        $cuti = Cuti::findOrFail($id);
        if ($cuti->status !== 'pending') {
            return response()->json(['error' => 'Pengajuan yang sudah diproses tidak bisa dihapus'], 400);
        }

        $cuti->delete();

        return response()->json(['message' => 'success'], 200);
    }
}
