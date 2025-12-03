<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\DaftarPasien;
use App\Models\Patient;
use App\Models\TindakanPasien;
use App\Models\SOAPE;
use App\Models\VitalSign;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PemeriksaanController extends Controller
{
    public function index($id)
    {
        $data = DaftarPasien::select([
            't_daftarpasien.pasienfk',
            't_daftarpasien.ruanganfk',
            't_daftarpasien.dokterfk',
            't_daftarpasien.id',
            't_daftarpasien.keluhan',
            't_daftarpasien.jenispembayaran',
            't_daftarpasien.statuspasien',
            't_daftarpasien.status',
            'm_pasien.id as idp',
            'm_pasien.nama',
            'm_pasien.umur',
            'm_pasien.alergi',
            'm_pasien.catatanpasien',
            'm_jeniskelamin.jeniskelamin',
            DB::raw('COALESCE(m_alamat.alamat, "-") as alamat'),
            'm_alamat.kelurahan',
            'm_alamat.kecamatan',
            'm_alamat.kotakabupaten',
            'm_alamat.provinsi',
            'm_pasien.tgllahir',
            DB::raw('COALESCE(m_study.name, "") as pendidikan'),
            DB::raw('COALESCE(m_pekerjaan.pekerjaan, "") as pekerjaan'),
            'm_pasien.norm',
            'm_ruangpraktek.name as ruangan',
            't_daftarpasien.noregistrasi',
        ])
            ->leftJoin('m_pasien', 'm_pasien.id', '=', 't_daftarpasien.pasienfk')
            ->leftJoin('m_jeniskelamin', 'm_jeniskelamin.id', '=', 'm_pasien.jeniskelaminfk')
            ->leftJoin('m_alamat', 'm_alamat.id', '=', 'm_pasien.alamatfk')
            ->leftJoin('m_study', 'm_study.id', '=', 'm_pasien.fk_study')
            ->leftJoin('m_pekerjaan', 'm_pekerjaan.id', '=', 'm_pasien.pekerjaanfk')
            ->leftJoin('m_ruangpraktek', 'm_ruangpraktek.id', '=', 't_daftarpasien.ruanganfk')
            ->where('t_daftarpasien.id', base64_decode(base64_decode($id)))->first();

        return view('pages/pemeriksaan/index', compact('data', 'id'));
    }

    public function vitalsign($id)
    {
        $status = DaftarPasien::select('status')->where('id', base64_decode(base64_decode($id)))->first();
        $vital_sign = VitalSign::where('fk_daftarpasien', base64_decode(base64_decode($id)))->first();
        return view('pages/pemeriksaan/content/vitalsign', compact('vital_sign', 'id', 'status'));
    }
    public function saveVitalSign(Request $request, $id)
    {
        try {

            // update jika ada id, kalau tidak maka insert baru
            $vitalSign = VitalSign::updateOrCreate(
                [
                    'fk_daftarpasien' => base64_decode(base64_decode($id)),
                    'klienfk' => Auth::user()->klienfk ?? 1,
                ],
                [
                    'siastole' => $request->siastole ?? null,
                    'diastole' => $request->diastole ?? null,
                    'tinggibadan' => $request->tinggibadan ?? null,
                    'beratbadan' => $request->beratbadan ?? null,
                    'suhu' => $request->suhu ?? null,
                    'nadi' => $request->nadi ?? null,
                    'pernapasan' => $request->pernapasan ?? null,
                    'lingkarperut' => $request->lingkarperut ?? null,
                ]
            );

            return response()->json([
                'code' => 1,
                'message' => 'Data Vital Sign berhasil disimpan.',
                'data' => $vitalSign
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'code' => 0,
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function updateKeluhan(Request $request, $id, $idp)
    {
        // dd($id, $idp);
        $idReal = base64_decode(base64_decode($id));
        try {
            $target = DaftarPasien::where('id', $idReal)->first();
            if (!$target) {
                return response()->json(['code' => 0, 'message' => 'Data pasien tidak ditemukan.']);
            }

            $target->keluhan = $request->value;
            $target->save();
            return response()->json([
                'code' => 1,
                'message' => 'Data keluhan berhasil disimpan.',
                'data' => $target
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'code' => 0,
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function updateAlergi(Request $request, $id, $idp)
    {
        try {
            $target = Patient::where('id', $idp)->first();
            if (!$target) {
                return response()->json(['code' => 0, 'message' => 'Data pasien tidak ditemukan.']);
            }

            $target->alergi = $request->value;
            $target->save();
            return response()->json([
                'code' => 1,
                'message' => 'Data alergi berhasil disimpan.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'code' => 0,
                'message' => $e->getMessage(),
                'error' => $e->getMessage(),
            ], 400);
        }
    }
    public function updateCatatan(Request $request, $id, $idp)
    {
        try {
            $target = Patient::where('id', $idp)->first();
            if (!$target) {
                return response()->json(['code' => 0, 'message' => 'Data pasien tidak ditemukan.']);
            }

            $target->catatanpasien = $request->value;
            $target->save();

            return response()->json([
                'code' => 1,
                'message' => 'Data catatan pasien berhasil disimpan.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'code' => 0,
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ], 400);
        }
    }


    public function soape($id)
    {
        $status = DaftarPasien::select('status')->where('id', base64_decode(base64_decode($id)))->first();
        $soap = SOAPE::where('fk_daftarpasien', base64_decode(base64_decode($id)))->first();
        $vitalsign = VitalSign::where('fk_daftarpasien', base64_decode(base64_decode($id)))->first();
        $objective = '';
        if ($vitalsign) {
            $objective = "sistole = {$vitalsign->siastole}, diastole = {$vitalsign->diastole}, TB = {$vitalsign->tinggibadan}, BB = {$vitalsign->beratbadan}, suhu = {$vitalsign->suhu}, nadi = {$vitalsign->nadi}, pernapasan = {$vitalsign->pernapasan}, lingkar perut = {$vitalsign->lingkarperut}";
        }

        $data = Patient::select([
            'm_pasien.is_diabet',
            'm_pasien.is_diare',
            'm_pasien.is_hipertensi',
            'm_pasien.is_tbc',
            'm_pasien.is_pnemonea'
        ])
            ->join('t_daftarpasien as a', 'a.pasienfk', 'm_pasien.id')
            ->where('a.id', base64_decode(base64_decode($id)))->first();
        return view('pages/pemeriksaan/content/soape', compact('soap', 'data', 'objective', 'id', 'status'));
    }

    public function saveSoape(Request $request, $id)
    {
        try {

            // update jika ada id, kalau tidak maka insert baru
            $soap = SOAPE::updateOrCreate(
                ['fk_daftarpasien' => base64_decode(base64_decode($id))],
                [
                    'klienfk' => Auth::user()->klienfk ?? 1,
                    'pegawaifk' => Auth::user()->id ?? 1,
                    's' => $request->s ?? null,
                    'o' => $request->o ?? null,
                    'a' => $request->a ?? null,
                    'p' => $request->p ?? null,
                    'e' => $request->e ?? null,
                ]
            );
            $data = DaftarPasien::select('pasienfk')->where('id', base64_decode(base64_decode($id)))->first();
            if (!$data) {
                throw new \Exception("Data pasien tidak ada.");
            }
            $patien = Patient::where('id', $data->pasienfk)->update(
                [
                    'is_diabet' => $request->diabet ? true : false,
                    'is_diare' => $request->diare ? true : false,
                    'is_hipertensi' => $request->hipertensi ? true : false,
                    'is_pnemonea' => $request->pnemonea ? true : false,
                    'is_tbc' => $request->tbc ? true : false,
                ]
            );

            return response()->json([
                'code' => 1,
                'message' => 'Data SOAPE berhasil disimpan.',
                'data' => $soap
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'code' => 0,
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function tindakan($id)
    {
        $status = DaftarPasien::select('status')->where('id', base64_decode(base64_decode($id)))->first();
        $tindakan = TindakanPasien::where('fk_daftarpasien', base64_decode(base64_decode($id)))->first();
        return view('pages/pemeriksaan/content/tindakan', compact('tindakan', 'id', 'status'));
    }

    public function saveTindakan(Request $request, $id)
    {
        $daftarpasienId = base64_decode(base64_decode($id));
        $klien = Auth::user()->klienfk ?? 1;

        DB::beginTransaction(); // START TRANSACTION

        try {

            // 1. Ambil semua tindakan_id dari request
            $incomingIds = collect($request->tindakan)
                ->pluck('tindakan_id')
                ->filter()
                ->toArray();

            // 2. Ambil semua tindakan_id yang sudah ada di DB
            $existingIds = TindakanPasien::where('klienfk', $klien)
                ->where('fk_daftarpasien', $daftarpasienId)
                ->pluck('fk_tindakan')
                ->toArray();

            // 3. Cari yang harus dihapus
            $deleteIds = array_diff($existingIds, $incomingIds);

            if (!empty($deleteIds)) {
                TindakanPasien::where('klienfk', $klien)
                    ->where('fk_daftarpasien', $daftarpasienId)
                    ->whereIn('fk_tindakan', $deleteIds)
                    ->delete();
            }

            // 4. Insert/update data yang dikirim
            foreach ($request->tindakan as $i) {
                TindakanPasien::updateOrCreate(
                    [
                        'klienfk'          => $klien,
                        'fk_daftarpasien'  => $daftarpasienId,
                        'fk_tindakan'      => $i['tindakan_id'] ?? null,
                    ],
                    [
                        'fk_dokter'       => $i['dokter_id'] ?? null,
                        'fk_userhandler'  => Auth::user()->id ?? null,
                        'tindakan'        => $i['tindakan_name'] ?? null,
                        'dokter'          => $i['dokter_text'] ?? null,
                        'amount'          => $i['tindakan_amount'] ?? null,
                        'qty'             => $i['qty'] ?? null,
                    ]
                );
            }

            DB::commit(); // sukses → commit

            return response()->json(['message' => 'success'], 200);
        } catch (\Throwable $e) {

            DB::rollBack(); // gagal → rollback

            return response()->json([
                'error' => 'Gagal menyimpan tindakan.',
                'detail' => $e->getMessage(),
            ], 400);
        }
    }


    public function updateStatus($id)
    {
        $daftarpasienId = base64_decode(base64_decode($id));

        try {
            // Cari data pasien
            $pasien = DaftarPasien::find($daftarpasienId);

            if (!$pasien) {
                return response()->json([
                    'error' => 'Data pasien tidak ditemukan'
                ], 400);
            }

            // Update status
            $pasien->status = 'Di Periksa';
            $pasien->tgldiperiksa = Carbon::now();
            $pasien->save();

            return response()->json([
                'message' => 'Status berhasil diperbarui'
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Gagal memperbarui status',
                'detail' => $e->getMessage()
            ], 400);
        }
    }
}
