<?php

namespace App\Http\Controllers\Website;

use App\Helpers\RawatInapHelper;
use App\Http\Controllers\Controller;
use App\Models\DaftarPasien;
use App\Models\Kamar;
use App\Models\RawatInap;
use App\Models\RawatInapCatatan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class RawatInapController extends Controller
{
    public function index()
    {
        return view('pages/rawat-inap/index');
    }

    public function getKamarList()
    {
        $data = Kamar::where('klienfk', Auth::user()->klienfk ?? 1)->orderBy('nama')->get();

        return response()->json(['message' => 'success', 'data' => $data], 200);
    }

    public function saveKamar(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|integer',
            'nama' => 'required|string|max:255',
            'kelas' => 'nullable|string|max:100',
            'tarif_per_hari' => 'required|numeric|min:0',
        ]);

        $kamar = $validated['id'] ?? null
            ? Kamar::where('klienfk', Auth::user()->klienfk ?? 1)->findOrFail($validated['id'])
            : new Kamar(['klienfk' => Auth::user()->klienfk ?? 1, 'status' => 'kosong']);

        $kamar->fill([
            'nama' => $validated['nama'],
            'kelas' => $validated['kelas'] ?? null,
            'tarif_per_hari' => $validated['tarif_per_hari'],
        ])->save();

        return response()->json(['message' => 'success'], 200);
    }

    public function deleteKamar($id)
    {
        $kamar = Kamar::where('klienfk', Auth::user()->klienfk ?? 1)->where('id', $id)->first();
        if ($kamar && $kamar->status === 'terisi') {
            return response()->json(['message' => 'error', 'error' => 'Kamar sedang terisi, tidak bisa dihapus'], 400);
        }
        $kamar?->delete();

        return response()->json(['message' => 'success'], 200);
    }

    public function getAvailableKamar()
    {
        $data = Kamar::where('klienfk', Auth::user()->klienfk ?? 1)->where('status', 'kosong')->orderBy('nama')->get();

        return response()->json(['message' => 'success', 'data' => $data], 200);
    }

    /**
     * Pencarian kunjungan (DaftarPasien) yang bisa diadmisi — belum punya
     * admisi rawat inap yang masih aktif, dan bukan kunjungan yang sudah
     * selesai/dibatalkan.
     */
    public function getDaftarPasienOptions(Request $request)
    {
        $q = $request->params;
        $klienfk = Auth::user()->klienfk ?? 1;

        $data = DaftarPasien::select(['t_daftarpasien.id', 't_daftarpasien.noregistrasi', 'm_pasien.nama', 'm_pasien.norm'])
            ->join('m_pasien', 'm_pasien.id', 't_daftarpasien.pasienfk')
            ->where('t_daftarpasien.klienfk', $klienfk)
            ->whereNotIn('t_daftarpasien.status', ['Selesai', 'Dibatalkan'])
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')
                    ->from('t_rawatinap')
                    ->whereColumn('t_rawatinap.fk_daftarpasien', 't_daftarpasien.id')
                    ->where('t_rawatinap.status', 'dirawat')
                    ->whereNull('t_rawatinap.deleted_at');
            })
            ->when($q, function ($query) use ($q) {
                $query->where('m_pasien.nama', 'like', "%{$q}%")->orWhere('m_pasien.norm', 'like', "%{$q}%");
            })
            ->limit(20)
            ->get();

        return response()->json(['message' => 'success', 'data' => $data], 200);
    }

    public function getDokterList(Request $request)
    {
        $q = $request->params;

        $data = User::select(['m_employee.id', 'm_employee.fullname'])
            ->join('m_employee', 'm_employee.id', 'm_users.fk_employee')
            ->where('m_employee.is_pemeriksapasien', 1)
            ->when($q, function ($query) use ($q) {
                $query->where('m_employee.fullname', 'like', "%{$q}%");
            })
            ->limit(20)
            ->get();

        return response()->json(['message' => 'success', 'data' => $data], 200);
    }

    public function getAdmissionList(Request $request)
    {
        $klienfk = Auth::user()->klienfk ?? 1;

        $data = RawatInap::select([
            't_rawatinap.*',
            'm_pasien.nama as pasien_nama',
            'm_pasien.norm as pasien_norm',
            'm_kamar.nama as kamar_nama',
            'm_employee.fullname as dokter_nama',
        ])
            ->join('m_pasien', 'm_pasien.id', 't_rawatinap.fk_pasien')
            ->join('m_kamar', 'm_kamar.id', 't_rawatinap.fk_kamar')
            ->leftJoin('m_employee', 'm_employee.id', 't_rawatinap.fk_dokter_penanggungjawab')
            ->where('t_rawatinap.klienfk', $klienfk)
            ->where('t_rawatinap.status', $request->input('status', 'dirawat'))
            ->orderByDesc('t_rawatinap.tgl_masuk')
            ->get();

        return response()->json(['message' => 'success', 'data' => $data], 200);
    }

    public function admit(Request $request)
    {
        try {
            $admission = app(RawatInapHelper::class)->admit([
                'klienfk' => Auth::user()->klienfk ?? 1,
                'fk_daftarpasien' => $request->fk_daftarpasien,
                'fk_kamar' => $request->fk_kamar,
                'fk_dokter_penanggungjawab' => $request->fk_dokter_penanggungjawab,
                'catatan_masuk' => $request->catatan_masuk,
                'fk_userhandler' => Auth::id(),
            ]);

            return response()->json(['message' => 'success', 'data' => $admission], 200);
        } catch (Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getBilling($id)
    {
        if (! $this->admissionBelongsToCurrentKlien($id)) {
            return response()->json(['error' => 'Admisi tidak ditemukan'], 404);
        }

        try {
            $billing = app(RawatInapHelper::class)->calculateBilling((int) $id);

            return response()->json(['message' => 'success', 'data' => $billing], 200);
        } catch (Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function discharge(Request $request, $id)
    {
        if (! $this->admissionBelongsToCurrentKlien($id)) {
            return response()->json(['error' => 'Admisi tidak ditemukan'], 404);
        }

        try {
            $admission = app(RawatInapHelper::class)->discharge((int) $id, [
                'ringkasan_pulang' => $request->ringkasan_pulang,
            ]);

            return response()->json(['message' => 'success', 'data' => $admission], 200);
        } catch (Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getCatatanList($id)
    {
        if (! $this->admissionBelongsToCurrentKlien($id)) {
            return response()->json(['error' => 'Admisi tidak ditemukan'], 404);
        }

        $data = RawatInapCatatan::where('fk_rawatinap', $id)->orderByDesc('waktu')->get();

        return response()->json(['message' => 'success', 'data' => $data], 200);
    }

    public function saveCatatan(Request $request, $id)
    {
        if (! $this->admissionBelongsToCurrentKlien($id)) {
            return response()->json(['error' => 'Admisi tidak ditemukan'], 404);
        }

        try {
            $catatan = app(RawatInapHelper::class)->tambahCatatan([
                'fk_rawatinap' => $id,
                'suhu' => $request->suhu,
                'nadi' => $request->nadi,
                'pernapasan' => $request->pernapasan,
                'tensi' => $request->tensi,
                'catatan' => $request->catatan,
                'fk_userhandler' => Auth::id(),
            ]);

            return response()->json(['message' => 'success', 'data' => $catatan], 200);
        } catch (Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * `RawatInapHelper` cuma menerima ID polos (bukan array berisi klienfk)
     * untuk billing/discharge/catatan, jadi pengecekan kepemilikan cabang
     * dilakukan di sini, sebelum memanggil helper — tanpa ini, staf dari
     * cabang lain bisa melihat/memulangkan/menambah catatan admisi milik
     * cabang yang bukan miliknya hanya dengan menebak ID lewat request langsung.
     */
    private function admissionBelongsToCurrentKlien($id): bool
    {
        return RawatInap::where('id', $id)->where('klienfk', Auth::user()->klienfk ?? 1)->exists();
    }
}
