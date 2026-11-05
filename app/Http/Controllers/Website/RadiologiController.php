<?php

namespace App\Http\Controllers\Website;

use App\Helpers\LabRadiologyWorkflowHelper;
use App\Http\Controllers\Controller;
use App\Models\JenisPemeriksaanRadiologi;
use App\Models\RadiologiPasien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RadiologiController extends Controller
{
    public function index()
    {
        return view('pages/radiologi/index');
    }

    public function getMasterList(Request $request)
    {
        $query = JenisPemeriksaanRadiologi::where('klienfk', Auth::user()->klienfk ?? 1);
        $total = (clone $query)->count();
        $this->applyDataTableSearch($query, $request, ['nama', 'deskripsi']);

        return $this->dataTableResponse($query->orderBy('nama')->paginate($this->dataTableLength($request), ['*'], 'page', $this->dataTablePage($request)), $request, $total);
    }

    public function saveMaster(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|integer',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:255',
            'harga' => 'required|numeric|min:0',
        ]);

        $jenis = $validated['id'] ?? null
            ? JenisPemeriksaanRadiologi::where('klienfk', Auth::user()->klienfk ?? 1)->findOrFail($validated['id'])
            : new JenisPemeriksaanRadiologi(['klienfk' => Auth::user()->klienfk ?? 1]);

        $jenis->fill([
            'nama' => $validated['nama'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'harga' => $validated['harga'],
        ])->save();

        return response()->json(['message' => 'success'], 200);
    }

    public function deleteMaster($id)
    {
        JenisPemeriksaanRadiologi::where('klienfk', Auth::user()->klienfk ?? 1)->where('id', $id)->delete();

        return response()->json(['message' => 'success'], 200);
    }

    public function getWorklist(Request $request)
    {
        $klienfk = Auth::user()->klienfk ?? 1;

        $query = RadiologiPasien::select([
            't_radiologipasien.*',
            'm_pasien.nama as pasien_nama',
            'm_pasien.norm as pasien_norm',
        ])
            ->join('m_pasien', 'm_pasien.id', 't_radiologipasien.fk_pasien')
            ->where('t_radiologipasien.klienfk', $klienfk);

        if ($request->status) {
            $query->where('t_radiologipasien.status', $request->status);
        } else {
            $query->whereIn('t_radiologipasien.status', ['diminta', 'terjadwal']);
        }

        $total = (clone $query)->count();
        $this->applyDataTableSearch($query, $request, ['m_pasien.nama', 'm_pasien.norm', 't_radiologipasien.jenis_nama']);

        return $this->dataTableResponse($query->orderBy('t_radiologipasien.tgl_diminta')->paginate($this->dataTableLength($request), ['*'], 'page', $this->dataTablePage($request)), $request, $total);
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

    public function jadwalkan(Request $request, $id)
    {
        $validated = $request->validate([
            'tgl_jadwal' => 'required|date',
        ]);

        $order = RadiologiPasien::where('klienfk', Auth::user()->klienfk ?? 1)
            ->where('status', 'diminta')
            ->findOrFail($id);

        $order->update([
            'tgl_jadwal' => $validated['tgl_jadwal'],
            'status' => 'terjadwal',
            'fk_userhandler' => Auth::id(),
        ]);

        return response()->json(['message' => 'success'], 200);
    }

    public function saveHasil(Request $request, $id)
    {
        $validated = $request->validate([
            'hasil_interpretasi' => 'required|string',
            'gambar' => 'nullable|image|max:10240',
        ]);

        $order = RadiologiPasien::where('klienfk', Auth::user()->klienfk ?? 1)->findOrFail($id);

        $gambarPath = $order->gambar_path;
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = 'radiologi_'.$order->id.'_'.now()->format('YmdHis').'.'.strtolower($file->getClientOriginalExtension());
            $gambarPath = $file->storeAs('radiologi/hasil', $filename, 'public');
        }

        $order->update([
            'hasil_interpretasi' => $validated['hasil_interpretasi'],
            'gambar_path' => $gambarPath,
            'status' => 'selesai',
            'fk_dokter_radiologi' => Auth::id(),
            'tgl_hasil' => now(),
        ]);

        return response()->json(['message' => 'success'], 200);
    }

    public function approveReport($id)
    {
        $order = RadiologiPasien::where('klienfk', Auth::user()->klienfk ?? 1)->findOrFail($id);
        $order = LabRadiologyWorkflowHelper::approveRadiologyReport($order, Auth::id());

        return response()->json(['message' => 'Laporan radiologi berhasil disetujui.', 'data' => $order]);
    }
}
