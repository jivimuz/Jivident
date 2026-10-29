<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Alamat;
use App\Models\DaftarPasien;
use App\Models\DiagnosaPasien;
use App\Models\Etnis;
use App\Models\JenisKelamin;
use App\Models\Kebangsaan;
use App\Models\Kelurahan;
use App\Models\MarialStatus;
use App\Models\Negara;
use App\Models\Patient;
use App\Models\Pekerjaan;
use App\Models\Religion;
use App\Models\RuangPraktek;
use App\Models\Setting;
use App\Models\Study;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PatientController extends Controller
{
    public function index()
    {
        return view('pages/patient/index');
    }

    public function getList(Request $request)
    {
        $offset = $request->input('start', 0);
        $limit = $request->input('length', 10);
        $searchValue = $request->input('search.value', '');
        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderDirection = $request->input('order.0.dir', 'desc');
        // $nik = $request->post('nik_search');

        $columns = [
            'id',
            'nama',
            'norm',
            'nik',
            'nobpjs',
            'nohp',
            'tgllahir',
        ];

        $orderColumnName = $columns[$orderColumnIndex] ?? 'id';

        // Base query
        $query = Patient::query()->select($columns);

        // Filter by NIK (kalau ada)
        // if ($nik) {
        //     $query->where('nik', $nik);
        // }

        // Search global
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($columns, $searchValue) {
                foreach ($columns as $col) {
                    $q->orWhere($col, 'like', "%{$searchValue}%");
                }
            });
        }

        // Total record sebelum filter
        $totalData = Patient::count();

        // Total setelah filter
        $filteredData = $query->count();

        // Sorting dan pagination
        $query->orderBy($orderColumnName, $orderDirection)
            ->skip($offset)
            ->take($limit);

        $data = $query->get();

        // Convert tanggal ke WIB
        $data->transform(function ($item) {
            if ($item->tgllahir) {
                $item->tgllahir = \Carbon\Carbon::parse($item->tgllahir)
                    ->setTimezone('Asia/Jakarta')
                    ->translatedFormat('d M Y H:i'); // pakai bahasa Indo otomatis
            }
            return $item;
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $filteredData,
            'data' => $data,
        ]);
    }

    public function addPatient()
    {
        $jk = JenisKelamin::all();
        $agama = Religion::all();
        $statusperkawinan  = MarialStatus::all();
        $pendidikan  = Study::all();
        $pekerjaan  = Pekerjaan::all();
        $kebangsaan   = Kebangsaan::all();
        $etnis    = Etnis::all();
        $negara    = Negara::all();
        return view('pages/patient/add_patient', compact('jk', 'agama', 'statusperkawinan', 'pendidikan', 'pekerjaan', 'kebangsaan', 'etnis', 'negara'));
    }

    public function editPatient($id)
    {
        $jk = JenisKelamin::all();
        $agama = Religion::all();
        $statusperkawinan  = MarialStatus::all();
        $pendidikan  = Study::all();
        $pekerjaan  = Pekerjaan::all();
        $kebangsaan   = Kebangsaan::all();
        $etnis    = Etnis::all();
        $negara    = Negara::all();
        $data = Patient::select(
            'm_pasien.*',
            'a.alamat',
            'a.kodepos',
            'a.rt',
            'a.rw',
            'a.kelurahanfk',
            'a.kelurahan',
            'a.kecamatanfk',
            'a.kecamatan',
            'a.kotakabupatenfk',
            'a.kotakabupaten',
            'a.provinsifk',
            'a.provinsi'
        )
            ->leftJoin('m_alamat as a', 'a.pasienfk', 'm_pasien.id')
            ->where('m_pasien.id', base64_decode(base64_decode($id)))
            ->where('m_pasien.klienfk', Auth::user()->klienfk ?? 1)
            ->first();
        if (!$data) {
            return abort(404, 'Data pasien tidak ditemukan');
        }
        return view('pages/patient/edit_patient', compact('jk', 'agama', 'statusperkawinan', 'pendidikan', 'pekerjaan', 'kebangsaan', 'etnis', 'negara', 'data'));
    }

    public function searchAddress(Request $request)
    {
        $q = trim((string) $request->post('q', ''));

        // biar gak query pas user baru ketik 1-2 huruf
        if (mb_strlen($q) < 3) {
            return response()->json(["message" => "success", "data" => []], 200);
        }

        $like = $q . '%';

        $q1 = DB::table('m_kelurahan as kel')
            ->join('m_provinsi as prov', 'prov.id', '=', 'kel.provinsifk')
            ->join('m_kecamatan as kec', 'kec.id', '=', 'kel.kecamatanfk')
            ->join('m_kotakabupaten as kab', 'kab.id', '=', 'kel.kotakabupatenfk')
            ->select(['kel.id', 'kel.kelurahan', 'kec.kecamatan', 'kab.kotakabupaten', 'prov.provinsi', 'kel.kecamatanfk', 'kel.kotakabupatenfk', 'kel.provinsifk'])
            ->where('kel.kelurahan', 'like', $like)
            ->limit(20);

        $q2 = DB::table('m_kelurahan as kel')
            ->join('m_provinsi as prov', 'prov.id', '=', 'kel.provinsifk')
            ->join('m_kecamatan as kec', 'kec.id', '=', 'kel.kecamatanfk')
            ->join('m_kotakabupaten as kab', 'kab.id', '=', 'kel.kotakabupatenfk')
            ->select(['kel.id', 'kel.kelurahan', 'kec.kecamatan', 'kab.kotakabupaten', 'prov.provinsi', 'kel.kecamatanfk', 'kel.kotakabupatenfk', 'kel.provinsifk'])
            ->where('kec.kecamatan', 'like', $like)
            ->limit(20);

        $union = $q1->unionAll($q2);

        $results = DB::query()
            ->fromSub($union, 'x')
            ->select('*')
            ->distinct()
            ->limit(20)
            ->get();

        return response()->json(["message" => "success", "data" => $results], 200);
    }

    public function saveData(Request $request)
    {
        $klienfk = Auth::user()->klienfk ?? 1;

        $validator = Validator::make($request->all(), [
            'nik' => [
                'required',
                'string',
                'max:20',
                // unik per klien (kalau tabel ada kolom klienfk)
                Rule::unique('m_pasien', 'nik')->where(fn($q) => $q->where('klienfk', $klienfk)),
            ],
            'nama' => 'required|string|max:100',
            'nohp' => 'required|string|max:20',
            'jeniskelaminfk' => 'required',
            'tgllahir' => 'required|date',

            'namapenanggungjawab' => 'required|string',
            'hubungandenganpasien' => 'required|string',
            'nohppenanggungjawab' => 'required|string',

            'alamat' => 'required|string',
            'kelurahanfk' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first()
            ], 422);
        }

        try {
            $result = DB::transaction(function () use ($request, $klienfk) {

                // umur mending dihitung dari tgllahir biar konsisten
                $umur = Carbon::parse($request->tgllahir)->age;

                // 1) bikin pasien dulu (tanpa norm dulu)
                $pasien = Patient::create([
                    'klienfk' => $klienfk,
                    'norm' => null, // nanti diisi setelah punya ID
                    'nobpjs' => $request->nobpjs,
                    'nik' => $request->nik,
                    'nama' => $request->nama,
                    'umur' => $umur,
                    'nohp' => $request->nohp,
                    'jeniskelaminfk' => $request->jeniskelaminfk,
                    'tempatlahir' => $request->tempatlahir,
                    'tgllahir' => $request->tgllahir,
                    'namaayah' => $request->namaayah,
                    'namaibu' => $request->namaibu,
                    'agamafk' => $request->agamafk,
                    'statusperkawinanfk' => $request->statusperkawinanfk,
                    'golongandarah' => $request->golongandarah,
                    'pendidikanfk' => $request->pendidikanfk,
                    'pekerjaanfk' => $request->pekerjaanfk,
                    'namapenanggungjawab' => $request->namapenanggungjawab,
                    'hubungandenganpasien' => $request->hubungandenganpasien,
                    'nohppenanggungjawab' => $request->nohppenanggungjawab,
                    'noasuransilain' => $request->noasuransilain,
                    'notelprumah' => $request->notelprumah,
                    'bahasadikuasai' => $request->bahasadikuasai,
                    'kebangsaanfk' => $request->kebangsaanfk,
                    'etnisfk' => $request->etnisfk,
                    'negarafk' => $request->negarafk,
                ]);

                // 2) generate norm PAKAI ID biar anti tabrakan
                // contoh: ymdHis + id (kamu bisa sesuaikan formatnya)
                $pasien->norm = generateNoRM(Carbon::now()->format('ymdHis') . $pasien->id);
                $pasien->save();

                // 3) simpan alamat
                $alamat = Alamat::create([
                    'klienfk' => $klienfk,
                    'alamat' => $request->alamat,
                    'kodepos' => $request->kodepos,
                    'rt' => $request->rt,
                    'rw' => $request->rw,
                    'kelurahanfk' => $request->kelurahanfk,
                    'kelurahan' => $request->kelurahan,
                    'kecamatanfk' => $request->kecamatanfk,
                    'kecamatan' => $request->kecamatan,
                    'kotakabupatenfk' => $request->kotakabupatenfk,
                    'kotakabupaten' => $request->kotakabupaten,
                    'provinsifk' => $request->provinsifk,
                    'provinsi' => $request->provinsi,
                    'pasienfk' => $pasien->id,
                ]);

                // 4) link alamat ke pasien
                $pasien->alamatfk = $alamat->id;
                $pasien->save();

                return $pasien;
            });

            return response()->json([
                'success' => true,
                'message' => 'Data pasien berhasil disimpan.',
                'data' => $result
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage(),
            ], 400);
        }
    }

    public function updateData(Request $request)
    {
        // Cari pasiennya
        $pasien = Patient::where('id', $request->id)->where('klienfk', Auth::user()->klienfk ?? 1)->first();
        if (!$pasien) {
            return response()->json(['error' => 'Data pasien tidak ditemukan'], 404);
        }

        // Validasi
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|max:20|unique:m_pasien,nik,' . $request->id,
            'nama' => 'required|string|max:100',
            'umur' => 'required|numeric',
            'nohp' => 'required|string|max:20',
            'jeniskelaminfk' => 'required',
            'tgllahir' => 'required|date',
            'namapenanggungjawab' => 'required|string',
            'hubungandenganpasien' => 'required|string',
            'nohppenanggungjawab' => 'required|string',
            'alamat' => 'required|string',
            'kelurahanfk' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first()
            ], 422);
        }

        try {

            // Update pasien
            $pasien->update([
                'nobpjs' => $request->nobpjs,
                'nik' => $request->nik,
                'nama' => $request->nama,
                'umur' => $request->umur,
                'nohp' => $request->nohp,
                'jeniskelaminfk' => $request->jeniskelaminfk,
                'tempatlahir' => $request->tempatlahir,
                'tgllahir' => $request->tgllahir,
                'namaayah' => $request->namaayah,
                'namaibu' => $request->namaibu,
                'agamafk' => $request->agamafk,
                'statusperkawinanfk' => $request->statusperkawinanfk,
                'golongandarah' => $request->golongandarah,
                'pendidikanfk' => $request->pendidikanfk,
                'pekerjaanfk' => $request->pekerjaanfk,
                'namapenanggungjawab' => $request->namapenanggungjawab,
                'hubungandenganpasien' => $request->hubungandenganpasien,
                'nohppenanggungjawab' => $request->nohppenanggungjawab,
                'noasuransilain' => $request->noasuransilain,
                'notelprumah' => $request->notelprumah,
                'bahasadikuasai' => $request->bahasadikuasai,
                'kebangsaanfk' => $request->kebangsaanfk,
                'etnisfk' => $request->etnisfk,
                'negarafk' => $request->negarafk,
                'updated_at' => Carbon::now(),
            ]);
            $cekal = Alamat::where('pasienfk', $request->id)->where('id', $pasien->alamatfk)->first();
            $alamat_id = $pasien->alamatfk;

            if (!$cekal) {
                $cekal2 = Alamat::where('pasienfk', $request->id)->first();
                $alamat_id =  $cekal2 && $cekal2->id ? $cekal2->id : null;
                if (!$pasien->alamatfk) {
                    $al = Alamat::where('pasienfk', $request->id)->first();
                    $alamat_id = $al && $al->id ? $al->id : null;

                    if (!$al) {
                        $aId = Alamat::insertGetId([
                            'pasienfk' => $request->id,
                            'alamat' => $request->alamat,
                            'kodepos' => $request->kodepos,
                            'rt' => $request->rt,
                            'rw' => $request->rw,
                            'kelurahanfk' => $request->kelurahanfk,
                            'kelurahan' => $request->kelurahan,
                            'kecamatanfk' => $request->kecamatanfk,
                            'kecamatan' => $request->kecamatan,
                            'kotakabupatenfk' => $request->kotakabupatenfk,
                            'kotakabupaten' => $request->kotakabupaten,
                            'provinsifk' => $request->provinsifk,
                            'provinsi' => $request->provinsi,
                        ]);
                        $pasien->alamatfk =   $aId;
                        $pasien->save();
                        return response()->json([
                            'success' => true,
                            'message' => 'Data pasien berhasil diperbarui.',
                            'data' => $pasien
                        ]);
                    }
                }
            }
            $pasien->alamatfk =  $alamat_id;
            $pasien->save();

            Alamat::updateOrCreate([
                'id' => $alamat_id,
                'pasienfk' => $request->id
            ], [
                'alamat' => $request->alamat,
                'kodepos' => $request->kodepos,
                'rt' => $request->rt,
                'rw' => $request->rw,
                'kelurahanfk' => $request->kelurahanfk,
                'kelurahan' => $request->kelurahan,
                'kecamatanfk' => $request->kecamatanfk,
                'kecamatan' => $request->kecamatan,
                'kotakabupatenfk' => $request->kotakabupatenfk,
                'kotakabupaten' => $request->kotakabupaten,
                'provinsifk' => $request->provinsifk,
                'provinsi' => $request->provinsi,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Data pasien berhasil diperbarui.',
                'data' => $pasien
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal mengupdate data: ' . $e->getMessage(),
                'message' => 'Gagal mengupdate data: ' . $e->getMessage()
            ], 400);
        }
    }



    public function registerPatient($id)
    {
        $pasien = Patient::select(
            'm_pasien.*',
            'm_jeniskelamin.jeniskelamin',
            'm_alamat.alamat',
            'm_alamat.kelurahan',
            'm_alamat.kecamatan',
            'm_alamat.kotakabupaten',
            'm_alamat.provinsi',
            DB::raw('COALESCE(m_study.name, "") as pendidikan'),
            DB::raw('COALESCE(m_pekerjaan.pekerjaan, "") as pekerjaan')
        )
            ->leftJoin('m_jeniskelamin', 'm_jeniskelamin.id', 'm_pasien.jeniskelaminfk')
            ->leftJoin('m_alamat', 'm_alamat.pasienfk', 'm_pasien.id')
            ->leftJoin('m_study', 'm_study.id', 'm_pasien.fk_study')
            ->leftJoin('m_pekerjaan', 'm_pekerjaan.id', 'm_pasien.pekerjaanfk')
            ->where('m_pasien.id', base64_decode(base64_decode($id)))
            ->where('m_pasien.klienfk', Auth::user()->klienfk ?? 1)
            ->first();
        if (!$pasien) {
            return abort(404, 'Data pasien tidak ditemukan');
        }
        $registrasi =  Setting::where('parameter', 'patient_registration_price')->first();

        $ruangan = RuangPraktek::select(['m_ruangpraktek.*', 'm_users.id as dokterid', 'm_users.display_name as dokter'])
            ->join('m_users', 'm_ruangpraktek.fk_dokter', 'm_users.id')
            ->where('m_ruangpraktek.klienfk', Auth::user()->klienfk ?? 1)->get();

        $riwayat = DiagnosaPasien::select('m_icdx.name')->where('tipe_diagnosa', 'X')
            ->join('m_icdx', 't_diagnosapasien.fk_diagnosa', 'm_icdx.id')
            ->where('fk_pasien', base64_decode(base64_decode($id)))
            ->get();

        return view('pages/patient/register_patient', compact('id', 'pasien', 'ruangan', 'registrasi', 'riwayat'));
    }

    public function processPatient(Request $request, $id)
    {
        $klienfk = Auth::user()->klienfk ?? 1;
        $pasienId = base64_decode(base64_decode($id));

        if (! Patient::where('id', $pasienId)->where('klienfk', $klienfk)->exists()) {
            return response()->json([
                'error' => 'Data pasien tidak ditemukan',
                'message' => 'Data pasien tidak ditemukan'
            ], 404);
        }

        $ruangan = RuangPraktek::select(['fk_dokter', 'code'])
            ->where('klienfk', $klienfk)->where('id', $request->ruanganfk)->first();
        if (!$ruangan || !$ruangan->fk_dokter) {
            return response()->json([
                'error' => 'Tidak ada dokter di ruangan terpilih ',
                'message' => 'Tidak ada dokter di ruangan terpilih '
            ], 400);
        }
        $cek = DaftarPasien::select('id')->where('pasienfk', $pasienId)->whereDate('tglregistrasi', Carbon::parse($request->tglregistrasi)->format('Y-m-d'))->first();
        if ($cek) {
            return response()->json([
                'error' => 'Pasien sudah mendaftar hari ini',
                'message' => 'Pasien sudah mendaftar hari ini'
            ], 400);
        }
        $cekstatus = DaftarPasien::select('id')->where('pasienfk', $pasienId)->first();
        $cekAntrian = DaftarPasien::where('ruanganfk', $request->ruanganfk)
            ->whereDate('tglregistrasi', Carbon::parse($request->tglregistrasi)->format('Y-m-d'))
            ->count();
        $time = Carbon::now();
        $registrasi =  Setting::where('parameter', 'patient_registration_price')->first();

        $insert = DaftarPasien::insertGetId([
            'klienfk' => $klienfk,
            'noregistrasi' => 'RGX' . $time->format('Ymd') .  '-' . $time->format('Hi') . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'), 0, 4),
            'dokterfk' => $ruangan->fk_dokter,
            'pegawaifk' => Auth::user()->id ?? null,
            'ruanganfk' => $request->ruanganfk,
            'pasienfk' => $pasienId,
            'jenispembayaran' => $request->jenispembayaran,
            'tglregistrasi' => Carbon::parse($request->tglregistrasi),
            'statuspasien' => $cekstatus ? 'LAMA' : 'BARU',
            'keluhan' => $request->keluhan,
            'noantrian' => ($ruangan->code ?? 'A') . '-' . sprintf('%003s', ($cekAntrian + 1)),
            'biaya_registrasi' => $registrasi->value ?? 0,

        ]);
        if ($insert) {
            return response()->json(['success' => true, 'message' => 'Data pasien berhasil disimpan.', 'data' => $insert]);
        }
        return response()->json([
            'error' => 'Data gagal ditambahkan ',
            'message' => 'Data gagal ditambahkan '
        ], 400);
    }
}
