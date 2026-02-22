<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\ICDIX;
use App\Models\ICDX;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ICDController extends Controller
{
    public function index()
    {
        return view('pages/icd/index');
    }

    // =========================
    // DataTables JSON
    // =========================
    public function dataICDIX(Request $request)
    {
        $query = ICDIX::query();

        // Total sebelum filter
        $recordsTotal = (clone $query)->count();

        // Filter custom dari form
        if ($request->filled('kode')) {
            $query->where('code', 'like', '%' . $request->kode . '%');
        }

        if ($request->filled('nama')) {
            $query->where('name', 'like', '%' . $request->nama . '%');
        }

        // Global search DataTables (search[value])
        $searchValue = $request->input('search.value');
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('code', 'like', '%' . $searchValue . '%')
                    ->orWhere('name', 'like', '%' . $searchValue . '%');
            });
        }

        // Total setelah filter
        $recordsFiltered = (clone $query)->count();

        // Mapping index kolom DataTables -> nama kolom DB
        $columnMap = [
            0 => 'id',      // kolom nomor (dummy), aman pakai id
            1 => 'code',
            2 => 'name',
            3 => 'id',      // kolom aksi (dummy), aman pakai id
        ];

        // Order DataTables
        $orderColumnIndex = (int) $request->input('order.0.column', 0);
        $orderDir = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $orderColumn = $columnMap[$orderColumnIndex] ?? 'id';

        $query->orderBy($orderColumn, $orderDir);

        // Pagination DataTables
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        // Kalau length = -1 berarti ambil semua
        if ($length !== -1) {
            $query->skip($start)->take($length);
        }

        $rows = $query->get()->map(function ($r) {
            return [
                'id' => $r->id,
                'code' => $r->code,
                'name' => $r->name,
                'tarif' => (float) $r->tarif,
            ];
        });

        return response()->json([
            'draw' => (int) $request->input('draw', 1),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $rows,
        ]);
    }

    public function dataICDX(Request $request)
    {
        $query = ICDX::query();

        // Total sebelum filter
        $recordsTotal = (clone $query)->count();

        // Filter custom dari form
        if ($request->filled('kode')) {
            $query->where('code', 'like', '%' . $request->kode . '%');
        }

        if ($request->filled('nama')) {
            $query->where('name', 'like', '%' . $request->nama . '%');
        }

        // Global search DataTables (search[value])
        $searchValue = $request->input('search.value');
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('code', 'like', '%' . $searchValue . '%')
                    ->orWhere('name', 'like', '%' . $searchValue . '%');
            });
        }

        // Total setelah filter
        $recordsFiltered = (clone $query)->count();

        // Mapping index kolom DataTables -> nama kolom DB
        $columnMap = [
            0 => 'id',      // kolom nomor (dummy), aman pakai id
            1 => 'code',
            2 => 'name',
            3 => 'id',      // kolom aksi (dummy), aman pakai id
        ];

        // Order DataTables
        $orderColumnIndex = (int) $request->input('order.0.column', 0);
        $orderDir = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $orderColumn = $columnMap[$orderColumnIndex] ?? 'id';

        $query->orderBy($orderColumn, $orderDir);

        // Pagination DataTables
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        // Kalau length = -1 berarti ambil semua
        if ($length !== -1) {
            $query->skip($start)->take($length);
        }

        $rows = $query->get()->map(function ($r) {
            return [
                'id' => $r->id,
                'code' => $r->code,
                'name' => $r->name,
                'tarif' => (float) $r->tarif,
            ];
        });

        return response()->json([
            'draw' => (int) $request->input('draw', 1),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $rows,
        ]);
    }

    // =========================
    // Modal Views
    // =========================
    public function addICDIX()
    {
        return view('pages/icd/partials/form', [
            'tipe' => 9,
            'mode' => 'create',
            'row'  => null,
            'action' => route('icd.store.ix'),
            'method' => 'POST',
        ]);
    }

    public function addICDX()
    {
        return view('pages/icd/partials/form', [
            'tipe' => 10,
            'mode' => 'create',
            'row'  => null,
            'action' => route('icd.store.x'),
            'method' => 'POST',
        ]);
    }

    public function showICD(int $id, int $tipe)
    {
        $model = $this->resolveModelByType($tipe);
        $row = $model->findOrFail($id);

        return view('pages/icd/partials/show', compact('row', 'tipe'));
    }

    public function editICDIX(int $id)
    {
        $row = ICDIX::findOrFail($id);

        return view('pages/icd/partials/form', [
            'tipe' => 9,
            'mode' => 'edit',
            'row' => $row,
            'action' => route('icd.update.ix', $row->id),
            'method' => 'PUT',
        ]);
    }

    public function editICDX(int $id)
    {
        $row = ICDX::findOrFail($id);

        return view('pages/icd/partials/form', [
            'tipe' => 10,
            'mode' => 'edit',
            'row' => $row,
            'action' => route('icd.update.x', $row->id),
            'method' => 'PUT',
        ]);
    }

    // =========================
    // Store
    // =========================
    public function storeICDIX(Request $request)
    {
        $validated = $this->validateICD($request, 'm_icdix');

        ICDIX::create($validated + [
            'klienfk' => 1, // sesuaikan kalau multi tenant
        ]);

        return response()->json([
            'success' => true,
            'messages' => 'ICD IX berhasil ditambahkan',
        ]);
    }

    public function storeICDX(Request $request)
    {
        $validated = $this->validateICD($request, 'm_icdx');

        ICDX::create($validated + [
            'klienfk' => 1,
        ]);

        return response()->json([
            'success' => true,
            'messages' => 'ICD X berhasil ditambahkan',
        ]);
    }

    // =========================
    // Update
    // =========================
    public function updateICDIX(Request $request, int $id)
    {
        $row = ICDIX::findOrFail($id);

        $validated = $this->validateICD($request, 'm_icdix', $id);

        $row->update($validated);

        return response()->json([
            'success' => true,
            'messages' => 'ICD IX berhasil diupdate',
        ]);
    }

    public function updateICDX(Request $request, int $id)
    {
        $row = ICDX::findOrFail($id);

        $validated = $this->validateICD($request, 'm_icdx', $id);

        $row->update($validated);

        return response()->json([
            'success' => true,
            'messages' => 'ICD X berhasil diupdate',
        ]);
    }

    // =========================
    // Delete (soft delete)
    // =========================
    public function deleteICD(int $id, int $tipe)
    {
        $model = $this->resolveModelByType($tipe);
        $row = $model->findOrFail($id);
        $row->delete();

        return response()->json([
            'success' => true,
            'messages' => 'Data berhasil dihapus',
        ]);
    }

    // =========================
    // Helpers
    // =========================
    protected function resolveModelByType(int $tipe)
    {
        return match ($tipe) {
            9 => new ICDIX(),
            10 => new ICDX(),
            default => abort(404, 'Tipe ICD tidak valid'),
        };
    }

    protected function validateICD(Request $request, string $table, ?int $id = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:250'],
            'code' => [
                'required',
                'string',
                'max:100',
                Rule::unique($table, 'code')->ignore($id)->whereNull('deleted_at'),
            ],
            'tarif' => ['nullable', 'numeric', 'min:0'],
        ], [
            'name.required' => 'Nama wajib diisi',
            'code.required' => 'Kode wajib diisi',
            'code.unique' => 'Kode sudah digunakan',
        ]);
    }

    public function getDokterList(Request $request)
    {
        $q = $request->q;

        $data = Employee::select([
            'a.id',
            'a.display_name as name',
        ])
            ->join('m_users as a', 'a.fk_employee', 'm_employee.id')
            ->where('is_pemeriksapasien', true)
            ->where('klienfk', Auth::user()->klienfk)
            ->when($q, function ($query) use ($q) {
                $query->where('a.display_name', 'LIKE', "%{$q}%");
            })
            ->limit(20)
            ->get();

        return response()->json([
            "message" => 'success',
            "data" => $data
        ], 200);
    }

    public function getDiagnosaList(Request $request)
    {
        $q = $request->q;
        $data = [];
        if ($request->tipe == 'IX') {
            $data = ICDIX::where('klienfk', Auth::user()->klienfk)
                ->when($q, function ($query) use ($q) {
                    $query->where('a.diagnosa', 'LIKE', "%{$q}%");
                })
                ->limit(20)
                ->get();
        } else  if ($request->tipe == 'X') {
            $data = ICDX::where('klienfk', Auth::user()->klienfk)
                ->when($q, function ($query) use ($q) {
                    $query->where('a.diagnosa', 'LIKE', "%{$q}%");
                })
                ->limit(20)
                ->get();
        }

        return response()->json([
            "message" => 'success',
            "data" => $data
        ], 200);
    }
}
