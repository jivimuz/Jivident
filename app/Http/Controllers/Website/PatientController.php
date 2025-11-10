<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Alamat;
use App\Models\DaftarPasien;
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
use App\Models\Study;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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

    public function searchAddress(Request $request)
    {
        $searchTerm = $request->post('q');

        $results = DB::select("
            SELECT
                m_kelurahan.*,
                m_provinsi.provinsi,
                m_kotakabupaten.kotakabupaten,
                m_kecamatan.kecamatan
            FROM m_kelurahan
             JOIN m_provinsi ON m_provinsi.id = m_kelurahan.provinsifk
             JOIN m_kecamatan ON m_kecamatan.id = m_kelurahan.kecamatanfk
             JOIN m_kotakabupaten ON m_kotakabupaten.id = m_kelurahan.kotakabupatenfk
            WHERE
                m_kelurahan.kelurahan LIKE ?
                OR m_kecamatan.kecamatan LIKE ?
                OR m_kotakabupaten.kotakabupaten LIKE ?
                OR m_provinsi.provinsi LIKE ?
            LIMIT 30
        ", [
            "%{$searchTerm}%",
            "%{$searchTerm}%",
            "%{$searchTerm}%",
            "%{$searchTerm}%"
        ]);


        return response()->json(["message" => 'success', 'data' => $results], 200);
    }

    public function saveData(Request $request)
    {
        // Validasi input dasar
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|max:20|unique:m_pasien,nik',
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
        $lastId = Patient::select('id')->orderBy('id', 'desc')->first();
        try {
            $data = [
                'klienfk' => Auth::user()->klienfk ?? 1,
                'norm' => generateNoRM(date('is') . ($lastId ? $lastId->id + 1 : 1)),
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
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            $insert = Patient::create($data);
            $alamat = Alamat::insert([
                'klienfk' => Auth::user()->klienfk ?? 1,
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
                'pasienfk' => $insert->id,
            ]);


            return response()->json(['success' => true, 'message' => 'Data pasien berhasil disimpan.', 'data' => $insert]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal menyimpan data: ' . $e->getMessage(),
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
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
            ->where('m_pasien.id', base64_decode(base64_decode($id)))->first();

        $ruangan = RuangPraktek::select(['m_ruangpraktek.*', 'm_users.id as dokterid', 'm_users.display_name as dokter'])
            ->join('m_users', 'm_ruangpraktek.fk_dokter', 'm_users.id')
            ->where('m_ruangpraktek.klienfk', Auth::user()->klienfk ?? 1)->get();

        return view('pages/patient/register_patient', compact('id', 'pasien', 'ruangan'));
    }

    public function processPatient(Request $request, $id)
    {
        $ruangan = RuangPraktek::select(['fk_dokter', 'code'])
            ->where('klienfk', Auth::user()->klienfk ?? 1)->where('id', $request->ruanganfk)->first();
        if (!$ruangan || !$ruangan->fk_dokter) {
            return response()->json([
                'error' => 'Tidak ada dokter di ruangan terpilih ',
                'message' => 'Tidak ada dokter di ruangan terpilih '
            ], 400);
        }
        $cek = DaftarPasien::select('id')->where('pasienfk', base64_decode(base64_decode($id)))->whereDate('tglregistrasi', Carbon::parse($request->tglregistrasi)->format('Y-m-d'))->first();
        if ($cek) {
            return response()->json([
                'error' => 'Pasien sudah mendaftar hari ini',
                'message' => 'Pasien sudah mendaftar hari ini'
            ], 400);
        }
        $cekstatus = DaftarPasien::select('id')->where('pasienfk', base64_decode(base64_decode($id)))->first();
        $cekAntrian = DaftarPasien::where('ruanganfk', $request->ruanganfk)
            ->whereDate('tglregistrasi', Carbon::parse($request->tglregistrasi)->format('Y-m-d'))
            ->count();
        $time = Carbon::now();
        $insert = DaftarPasien::insertGetId([
            'klienfk' => Auth::user()->klienfk ?? 1,
            'noregistrasi' => 'RGX' . $time->format('Ymd') .  '-' . $time->format('Hi') . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'), 0, 4),
            'dokterfk' => $ruangan->fk_dokter,
            'pegawaifk' => Auth::user()->id ?? null,
            'ruanganfk' => $request->ruanganfk,
            'pasienfk' => base64_decode(base64_decode($id)),
            'jenispembayaran' => $request->jenispembayaran,
            'tglregistrasi' => Carbon::parse($request->tglregistrasi),
            'statuspasien' => $cekstatus ? 'LAMA' : 'BARU',
            'keluhan' => $request->keluhan,
            'noantrian' => ($ruangan->code ?? 'A') . '-' . sprintf('%003s', ($cekAntrian + 1)),

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
