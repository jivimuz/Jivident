<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Klien;
use Illuminate\Http\Request;

class KlienController extends Controller
{
    public function index()
    {
        return view('pages/klien/index');
    }

    public function getList(Request $request)
    {
        $search = $request->input('search.value');

        $query = Klien::query();
        if (! empty($search)) {
            $query->where('nama', 'like', "%{$search}%")
                ->orWhere('kodeklinik', 'like', "%{$search}%");
        }

        $recordsTotal = Klien::count();
        $recordsFiltered = $query->count();

        $data = $query->orderBy('id')
            ->skip((int) $request->input('start', 0))
            ->take((int) $request->input('length', 10))
            ->get();

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    /**
     * Daftar ringkas semua cabang — dipakai untuk dropdown "Cabang" di form user.
     */
    public function getActiveList()
    {
        return response()->json(['message' => 'success', 'data' => Klien::orderBy('nama')->get(['id', 'nama', 'kodeklinik'])], 200);
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);

        Klien::create($validated);

        return response()->json(['message' => 'success'], 200);
    }

    public function edit($id)
    {
        return response()->json(['message' => 'success', 'data' => Klien::findOrFail($id)], 200);
    }

    public function update(Request $request, $id)
    {
        $validated = $this->validateRequest($request);

        Klien::findOrFail($id)->update($validated);

        return response()->json(['message' => 'success'], 200);
    }

    public function destroy($id)
    {
        if (Klien::count() <= 1) {
            return response()->json(['error' => 'Tidak bisa menghapus cabang terakhir'], 400);
        }

        Klien::findOrFail($id)->delete();

        return response()->json(['message' => 'success'], 200);
    }

    private function validateRequest(Request $request): array
    {
        $validated = $request->validate([
            // kodeklinik/nama/tipe/nohp/email/alamat semua NOT NULL tanpa default di DB
            'kodeklinik' => 'required|string|max:30',
            'nama' => 'required|string|max:255',
            'tipe' => 'required|string|max:255',
            'nohp' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'alamat' => 'required|string|max:255',
            'isholding' => 'nullable|boolean',
            // ID Organisasi SATUSEHAT untuk cabang ini (opsional, diisi kalau sudah terdaftar)
            'ihs_id' => 'nullable|string|max:255',
        ]);

        $validated['isholding'] = $request->boolean('isholding');

        return $validated;
    }
}
