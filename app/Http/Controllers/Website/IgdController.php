<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\DaftarPasien;
use App\Models\Patient;
use App\Models\RuangPraktek;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

class IgdController extends Controller
{
    /**
     * Urutan prioritas triase — angka kecil = lebih diutamakan di antrian IGD.
     * Hitam (meninggal/tidak tertolong) ditaruh paling akhir, bukan dihapus
     * dari daftar, supaya tetap tercatat.
     */
    private const TRIASE_PRIORITY = ['Merah' => 1, 'Kuning' => 2, 'Hijau' => 3, 'Hitam' => 4];

    public function index()
    {
        return view('pages/igd/index');
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

    public function getRuanganList(Request $request)
    {
        $q = $request->params;

        $data = RuangPraktek::select(['m_ruangpraktek.*', 'm_users.display_name as dokter'])
            ->join('m_users', 'm_ruangpraktek.fk_dokter', 'm_users.id')
            ->where('m_ruangpraktek.klienfk', Auth::user()->klienfk ?? 1)
            ->when($q, function ($query) use ($q) {
                $query->where('m_ruangpraktek.name', 'like', "%{$q}%");
            })
            ->limit(20)
            ->get();

        return response()->json(['message' => 'success', 'data' => $data], 200);
    }

    /**
     * Antrian IGD diurutkan berdasarkan prioritas triase, BUKAN waktu daftar
     * (FIFO) seperti antrian rawat jalan biasa — pasien Merah selalu di atas
     * Kuning meskipun daftar lebih belakangan.
     */
    public function getQueueList()
    {
        $klienfk = Auth::user()->klienfk ?? 1;
        $today = now('Asia/Jakarta')->toDateString();

        $data = DaftarPasien::select([
            't_daftarpasien.id',
            't_daftarpasien.noregistrasi',
            't_daftarpasien.noantrian',
            't_daftarpasien.triase',
            't_daftarpasien.status',
            't_daftarpasien.tglregistrasi',
            't_daftarpasien.keluhan',
            'm_pasien.nama as pasien_nama',
            'm_pasien.norm as pasien_norm',
            'm_ruangpraktek.name as ruangan',
        ])
            ->join('m_pasien', 'm_pasien.id', 't_daftarpasien.pasienfk')
            ->leftJoin('m_ruangpraktek', 'm_ruangpraktek.id', 't_daftarpasien.ruanganfk')
            ->where('t_daftarpasien.klienfk', $klienfk)
            ->whereNotNull('t_daftarpasien.triase')
            ->whereDate('t_daftarpasien.tglregistrasi', $today)
            ->whereNotIn('t_daftarpasien.status', ['Selesai', 'Dibatalkan'])
            ->get();

        $sorted = $data->sortBy(function ($row) {
            return [self::TRIASE_PRIORITY[$row->triase] ?? 99, $row->tglregistrasi];
        })->values();

        return response()->json(['message' => 'success', 'data' => $sorted], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pasienfk' => 'required|integer',
            'ruanganfk' => 'required|integer',
            'triase' => ['required', Rule::in(array_keys(self::TRIASE_PRIORITY))],
            'keluhan' => 'nullable|string|max:255',
        ]);

        try {
            $klienfk = Auth::user()->klienfk ?? 1;

            $ruangan = RuangPraktek::where('klienfk', $klienfk)->where('id', $validated['ruanganfk'])->first();
            if (! $ruangan || ! $ruangan->fk_dokter) {
                throw new InvalidArgumentException('Ruangan tidak ditemukan atau belum ada dokter yang bertugas');
            }

            $time = Carbon::now();
            $sudahPernahDaftar = DaftarPasien::where('pasienfk', $validated['pasienfk'])->exists();
            $jumlahAntrianIgdHariIni = DaftarPasien::whereNotNull('triase')
                ->whereDate('tglregistrasi', $time->toDateString())
                ->count();
            $registrasi = Setting::where('parameter', 'patient_registration_price')->first();

            $daftar = DaftarPasien::create([
                'klienfk' => $klienfk,
                'noregistrasi' => 'IGD'.$time->format('Ymd').'-'.substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'), 0, 4),
                'dokterfk' => $ruangan->fk_dokter,
                'pegawaifk' => Auth::id(),
                'ruanganfk' => $validated['ruanganfk'],
                'pasienfk' => $validated['pasienfk'],
                'jenispembayaran' => 'UMUM',
                'tglregistrasi' => $time,
                'statuspasien' => $sudahPernahDaftar ? 'LAMA' : 'BARU',
                'status' => 'Dalam Antrian',
                'triase' => $validated['triase'],
                'keluhan' => $validated['keluhan'] ?? null,
                'noantrian' => 'IGD-'.sprintf('%03d', $jumlahAntrianIgdHariIni + 1),
                'biaya_registrasi' => $registrasi->value ?? 0,
            ]);

            return response()->json(['message' => 'success', 'data' => $daftar], 200);
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], 400);
        }
    }

    /**
     * Pasien bisa dinilai ulang triase-nya kalau kondisinya berubah saat masih
     * menunggu (memburuk atau membaik).
     */
    public function updateTriase(Request $request, $id)
    {
        $validated = $request->validate([
            'triase' => ['required', Rule::in(array_keys(self::TRIASE_PRIORITY))],
        ]);

        $daftar = DaftarPasien::where('klienfk', Auth::user()->klienfk ?? 1)->findOrFail($id);
        $daftar->update(['triase' => $validated['triase']]);

        return response()->json(['message' => 'success'], 200);
    }
}
