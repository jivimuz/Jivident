<?php

namespace App\Http\Controllers\Website;

use App\Helpers\LabRadiologyWorkflowHelper;
use App\Http\Controllers\Controller;
use App\Models\JenisPemeriksaanLab;
use App\Models\LabPasien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LabController extends Controller
{
    public function index()
    {
        return view('pages/lab/index');
    }

    public function getMasterList(Request $request)
    {
        $query = JenisPemeriksaanLab::where('klienfk', Auth::user()->klienfk ?? 1);
        $total = (clone $query)->count();
        $this->applyDataTableSearch($query, $request, ['nama', 'satuan', 'nilai_rujukan']);

        return $this->dataTableResponse($query->orderBy('nama')->paginate($this->dataTableLength($request), ['*'], 'page', $this->dataTablePage($request)), $request, $total);
    }

    public function saveMaster(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|integer',
            'nama' => 'required|string|max:255',
            'satuan' => 'nullable|string|max:50',
            'nilai_rujukan' => 'nullable|string|max:255',
            'harga' => 'required|numeric|min:0',
        ]);

        $jenis = $validated['id'] ?? null
            ? JenisPemeriksaanLab::where('klienfk', Auth::user()->klienfk ?? 1)->findOrFail($validated['id'])
            : new JenisPemeriksaanLab(['klienfk' => Auth::user()->klienfk ?? 1]);

        $jenis->fill([
            'nama' => $validated['nama'],
            'satuan' => $validated['satuan'] ?? null,
            'nilai_rujukan' => $validated['nilai_rujukan'] ?? null,
            'harga' => $validated['harga'],
        ])->save();

        return response()->json(['message' => 'success'], 200);
    }

    public function deleteMaster($id)
    {
        JenisPemeriksaanLab::where('klienfk', Auth::user()->klienfk ?? 1)->where('id', $id)->delete();

        return response()->json(['message' => 'success'], 200);
    }

    public function getWorklist(Request $request)
    {
        $klienfk = Auth::user()->klienfk ?? 1;

        $query = LabPasien::select([
            't_labpasien.*',
            'm_pasien.nama as pasien_nama',
            'm_pasien.norm as pasien_norm',
        ])
            ->join('m_pasien', 'm_pasien.id', 't_labpasien.fk_pasien')
            ->where('t_labpasien.klienfk', $klienfk);

        if ($request->status) {
            $query->where('t_labpasien.status', $request->status);
        } else {
            $query->where('t_labpasien.status', 'diminta');
        }

        $total = (clone $query)->count();
        $this->applyDataTableSearch($query, $request, ['m_pasien.nama', 'm_pasien.norm', 't_labpasien.jenis_nama']);

        return $this->dataTableResponse($query->orderBy('t_labpasien.tgl_diminta')->paginate($this->dataTableLength($request), ['*'], 'page', $this->dataTablePage($request)), $request, $total);
    }

    private function applyDataTableSearch($query, Request $request, array $columns): void
    {
        $search = $request->input('search.value');
        if ($search) {
            $query->where(function ($builder) use ($columns, $search) {
                foreach ($columns as $column) {
                    $builder->orWhere($column, 'like', "%{$search}%");
                }
            });
        }
    }

    private function dataTableLength(Request $request): int
    {
        return min(max((int) $request->input('length', 10), 1), 100);
    }

    private function dataTablePage(Request $request): int
    {
        return (int) floor((int) $request->input('start', 0) / $this->dataTableLength($request)) + 1;
    }

    private function dataTableResponse($paginator, Request $request, int $total)
    {
        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $total,
            'recordsFiltered' => $paginator->total(),
            'data' => $paginator->items(),
        ]);
    }

    public function saveHasil(Request $request, $id)
    {
        $validated = $request->validate([
            'hasil' => 'required|string|max:255',
            'hasil_abnormal' => 'nullable|boolean',
        ]);

        $order = LabPasien::where('klienfk', Auth::user()->klienfk ?? 1)->findOrFail($id);
        $order->update([
            'hasil' => $validated['hasil'],
            'hasil_abnormal' => $request->boolean('hasil_abnormal'),
            'status' => 'selesai',
            'fk_petugas_input' => Auth::id(),
            'tgl_hasil' => now(),
        ]);

        return response()->json(['message' => 'success'], 200);
    }

    public function collectSpecimen($id)
    {
        $order = LabPasien::where('klienfk', Auth::user()->klienfk ?? 1)->findOrFail($id);
        $order = LabRadiologyWorkflowHelper::collectSpecimen($order, Auth::id());

        return response()->json(['message' => 'Spesimen berhasil diambil.', 'data' => $order]);
    }

    public function verifyResult($id)
    {
        $order = LabPasien::where('klienfk', Auth::user()->klienfk ?? 1)->findOrFail($id);
        $order = LabRadiologyWorkflowHelper::verifyLabResult($order, Auth::id());

        return response()->json(['message' => 'Hasil laboratorium berhasil diverifikasi.', 'data' => $order]);
    }
}
