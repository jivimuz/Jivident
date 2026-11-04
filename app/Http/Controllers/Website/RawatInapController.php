<?php

namespace App\Http\Controllers\Website;

use App\Helpers\RawatInapHelper;
use App\Http\Controllers\Controller;
use App\Models\DaftarPasien;
use App\Models\Kamar;
use App\Models\RawatInap;
use App\Models\RawatInapCarePlan;
use App\Models\RawatInapCatatan;
use App\Models\RawatInapMedicalRecord;
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
     * Rooms with their beds nested underneath, each bed carrying the active
     * admission's patient name/entry date when occupied — powers the bed
     * grid visualization (room management card and the admission picker)
     * without a separate request per bed. Shared with PemeriksaanController's
     * Rawat Inap tab (same picker, used there for room transfers).
     */
    public function getRuanganList()
    {
        $data = app(RawatInapHelper::class)->getRuanganListData(Auth::user()->klienfk ?? 1);

        return response()->json(['message' => 'success', 'data' => $data], 200);
    }

    public function saveRuangan(Request $request)
    {
        $validated = $request->validate([
            'id' => 'nullable|integer',
            'nama' => 'required|string|max:255',
            'lantai' => 'nullable|string|max:100',
            'kelas' => 'nullable|string|max:100',
            'tarif_per_hari' => 'required|numeric|min:0',
            'jumlah_ranjang' => 'required|integer|min:1|max:50',
        ]);

        try {
            $klienfk = Auth::user()->klienfk ?? 1;
            $helper = app(RawatInapHelper::class);
            $data = array_merge($validated, ['klienfk' => $klienfk]);

            $ruangan = ! empty($validated['id'])
                ? $helper->updateRuangan((int) $validated['id'], $data)
                : $helper->createRuangan($data);

            return response()->json(['message' => 'success', 'data' => $ruangan], 200);
        } catch (Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function deleteRuangan($id)
    {
        try {
            app(RawatInapHelper::class)->deleteRuangan((int) $id, Auth::user()->klienfk ?? 1);

            return response()->json(['message' => 'success'], 200);
        } catch (Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
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

    /**
     * Room-transfer history for an admission — same log PemeriksaanController's
     * Rawat Inap tab writes to when staff move a patient to a different bed.
     * Surfaced here too so it's visible from the main admission list, not
     * only from inside Pemeriksaan.
     */
    public function getPindahLog($id)
    {
        if (! $this->admissionBelongsToCurrentKlien($id)) {
            return response()->json(['error' => 'Admisi tidak ditemukan'], 404);
        }

        $data = app(RawatInapHelper::class)->getPindahLog((int) $id);

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
                'kesadaran' => $request->kesadaran,
                'gcs' => $request->gcs,
                'spo2' => $request->spo2,
                'skala_nyeri' => $request->skala_nyeri,
                'intake_ml' => $request->intake_ml,
                'output_ml' => $request->output_ml,
                'catatan' => $request->catatan,
                'intervensi' => $request->intervensi,
                'respons' => $request->respons,
                'fk_userhandler' => Auth::id(),
            ]);

            return response()->json(['message' => 'success', 'data' => $catatan], 200);
        } catch (Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getCarePlans($id)
    {
        if (! $this->admissionBelongsToCurrentKlien($id)) {
            return response()->json(['error' => 'Admisi tidak ditemukan'], 404);
        }

        $data = RawatInapCarePlan::where('fk_rawatinap', $id)
            ->orderByRaw("CASE status WHEN 'aktif' THEN 0 WHEN 'teratasi' THEN 1 ELSE 2 END")
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['message' => 'success', 'data' => $data], 200);
    }

    public function saveCarePlan(Request $request, $id)
    {
        if (! $this->admissionBelongsToCurrentKlien($id)) {
            return response()->json(['error' => 'Admisi tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'diagnosis' => 'required|string|max:255',
            'goal' => 'required|string|max:5000',
            'interventions' => 'required|string|max:5000',
            'target_date' => 'nullable|date',
        ]);

        $admission = RawatInap::findOrFail($id);
        if ($admission->status !== 'dirawat') {
            return response()->json(['error' => 'Care plan hanya dapat ditambahkan saat pasien masih dirawat'], 400);
        }

        $plan = RawatInapCarePlan::create([
            'fk_rawatinap' => $id,
            ...$validated,
            'created_by' => Auth::id(),
        ]);

        return response()->json(['message' => 'success', 'data' => $plan], 200);
    }

    public function evaluateCarePlan(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:aktif,teratasi,dihentikan',
            'evaluation' => 'required|string|max:5000',
        ]);

        $plan = RawatInapCarePlan::where('id', $id)
            ->whereHas('admission', fn ($query) => $query->where('klienfk', Auth::user()->klienfk ?? 1))
            ->first();

        if (! $plan) {
            return response()->json(['error' => 'Care plan tidak ditemukan'], 404);
        }

        $plan->update([
            ...$validated,
            'evaluated_by' => Auth::id(),
            'evaluated_at' => now(),
        ]);

        return response()->json(['message' => 'success', 'data' => $plan->refresh()], 200);
    }

    public function getMedicalRecords($id)
    {
        if (! $this->admissionBelongsToCurrentKlien($id)) {
            return response()->json(['error' => 'Admisi tidak ditemukan'], 404);
        }

        $data = RawatInapMedicalRecord::where('fk_rawatinap', $id)
            ->orderByDesc('id')
            ->get();

        return response()->json(['message' => 'success', 'data' => $data], 200);
    }

    public function saveMedicalRecord(Request $request, $id)
    {
        if (! $this->admissionBelongsToCurrentKlien($id)) {
            return response()->json(['error' => 'Admisi tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'type' => 'required|in:cppt,instruksi_dokter,resume_medis,surat_kontrol',
            'subjective' => 'nullable|string|max:10000',
            'objective' => 'nullable|string|max:10000',
            'assessment' => 'nullable|string|max:10000',
            'plan' => 'nullable|string|max:10000',
            'instruction' => 'nullable|string|max:10000',
            'diagnosis_final' => 'nullable|string|max:10000',
            'procedure_summary' => 'nullable|string|max:10000',
            'condition_discharge' => 'nullable|string|max:10000',
            'follow_up_date' => 'nullable|date',
            'follow_up_destination' => 'nullable|string|max:255',
        ]);

        $record = RawatInapMedicalRecord::create([
            'fk_rawatinap' => $id,
            ...$validated,
            'created_by' => Auth::id(),
        ]);

        return response()->json(['message' => 'success', 'data' => $record], 200);
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
