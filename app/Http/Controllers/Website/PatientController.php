<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Etnis;
use App\Models\JenisKelamin;
use App\Models\Kebangsaan;
use App\Models\Kelurahan;
use App\Models\MarialStatus;
use App\Models\Negara;
use App\Models\Patient;
use App\Models\Pekerjaan;
use App\Models\Religion;
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
                'norm' => generateNoRM(date('i:s') . ($lastId ? $lastId->id + 1 : 1)),
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
                'kebangsaanfk' => $request->kebangsaanfk,
                'etnisfk' => $request->etnisfk,
                'negarafk' => $request->negarafk,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            Patient::create($data);

            return response()->json(['success' => true, 'message' => 'Data pasien berhasil disimpan.']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal menyimpan data: ' . $e->getMessage(),
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 400);
        }
    }
}
