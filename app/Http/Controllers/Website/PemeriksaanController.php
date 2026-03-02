<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\DaftarPasien;
use App\Models\DiagnosaPasien;
use App\Models\Patient;
use App\Models\PenggunaanProduct;
use App\Models\Product;
use App\Models\ResepPasien;
use App\Models\Setting;
use App\Models\TindakanPasien;
use App\Models\SOAPE;
use App\Models\VitalSign;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

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
        if ($data) {
            return view('pages/pemeriksaan/index', compact('data', 'id'));
        }
        return redirect()->back();
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
                        'price'          => $i['tindakan_amount'] ?? 0,
                        'amount'          => ($i['tindakan_amount'] ?? 0) * ($i['qty'] ?? 1),
                        'qty'             => $i['qty'] ?? 1,
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


    public function updateStatus(Request $request, $id)
    {
        $daftarPasienID = base64_decode(base64_decode($id));

        try {
            // Cari data pasien
            $pasien = DaftarPasien::find($daftarPasienID);

            if (!$pasien) {
                return response()->json([
                    'error' => 'Data pasien tidak ditemukan'
                ], 400);
            }

            if ((int)$request->f == 1) {
                $pasien->status = 'Payment';
                $daftarPasienID = ($daftarPasienID === '' || $daftarPasienID === 0) ? null : $daftarPasienID;

                $totalPaid = (float) DB::table('t_transactions')
                    ->where('daftarpasien_id', $daftarPasienID)
                    ->selectRaw("COALESCE(SUM(CASE
                        WHEN transaction_type='in' THEN amount
                        WHEN transaction_type='out' THEN -amount
                        ELSE 0 END),0) as total_paid")->whereNull('deleted_at')
                    ->value('total_paid');

                $tagihanTindakan = (float) DB::table('t_tindakanpasien')
                    ->where('fk_daftarpasien', $daftarPasienID)
                    ->selectRaw("COALESCE(SUM(amount),0) as total_amount")->whereNull('deleted_at')
                    ->value('total_amount');
                $tagihanDiagnosa = (float) DB::table('t_diagnosapasien')
                    ->where('fk_daftarpasien', $daftarPasienID)
                    ->selectRaw("COALESCE(SUM(amount),0) as total_amount")->whereNull('deleted_at')
                    ->value('total_amount');

                $registrasi =  Setting::where('parameter', 'patient_registration_price')->first();

                $price = (float)($tagihanTindakan ?? 0) + (float)($tagihanDiagnosa ?? 0) + (float) ($registrasi ? $registrasi->value : 50000);

                $newStatus = $this->resolvePaymentStatus($totalPaid, $price,  $pasien->statuspembayaran);

                // Update status kalau berubah
                if (($pasien->statuspembayaran ?? null) !== $newStatus) {

                    $pasien->statuspembayaran = $newStatus;
                }
                if ($newStatus == 'Verifikasi' || $newStatus == 'Lunas') {
                    $pasien->status = 'Selesai';
                }
            } else {
                $pasien->status = 'Di Periksa';
                $pasien->tgldiperiksa = Carbon::now();
            }

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


    public function resep($id)
    {
        $status = DaftarPasien::select(['status', 'pasienfk'])->where('id', base64_decode(base64_decode($id)))->first();
        $racikanprice = Setting::select('value')->where('parameter', 'patient_jasa_racikan_price')->first();

        return view('pages/pemeriksaan/content/resep', compact('id', 'status', 'racikanprice'));
    }

    public function getCurrentResep($id)
    {
        try {
            $daftarPasienId = base64_decode(base64_decode($id), true);

            if (!$daftarPasienId || !is_numeric($daftarPasienId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID tidak valid'
                ], 422);
            }

            // Parent resep (aktif saja)
            $resepRows = ResepPasien::query()
                ->where('daftarpasien_id', (int) $daftarPasienId)
                ->whereNull('deleted_at')
                ->orderBy('r_num')
                ->orderBy('id')
                ->get();

            if ($resepRows->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ], 200);
            }

            // Ambil semua detail racikan untuk resep yang ada
            $detailRows = DB::table('t_reseppasienracikan')
                ->whereIn('reseppasien_id', $resepRows->pluck('id'))
                ->orderBy('id')
                ->get([
                    'id',
                    'reseppasien_id',
                    'product_id as id_product',
                    'product_name as name',
                    'qty',
                ])
                ->groupBy('reseppasien_id');

            // Format hasil supaya cocok dengan payload cart frontend kamu
            $data = $resepRows->map(function ($row) use ($detailRows) {
                $ingredients = collect($detailRows[$row->id] ?? [])->map(function ($d) {
                    return [
                        'id'   => (int) $d->id_product,
                        'name' => $d->name,
                        'qty'  => (float) $d->qty,
                    ];
                })->values();

                return [
                    'id'            => $row->id,
                    'type'          => $row->type, // non-racik / racik
                    'r_num'         => (int) $row->r_num,
                    'obat_id'       => $row->obat_id,
                    'nama_racikan'  => $row->nama_racikan,
                    'bentuk'        => $row->bentuk,
                    'obat_name'     => $row->obat_name,
                    'qty'           => (float) $row->qty,
                    'qty_buat'      => (float) $row->qty_buat,
                    'harga_satuan'  => (float) $row->harga_satuan,
                    'satuan'        => $row->unit, // db=unit -> frontend=satuan
                    'penggunaan_id' => $row->penggunaan_id,
                    'signa'         => $row->signa,
                    'subtotal'      => (float) $row->subtotal,
                    'jasa_racik'    => (float) $row->jasa_racik,
                    'ingredients'   => $row->type === 'racik' ? $ingredients : [],
                ];
            })->values();

            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data resep',
                'error' => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function getProductList(Request $request)
    {
        $q = $request->q;
        $locationId = $request->location_id ?? 1;
        $klienFk = Auth::user()->klienfk ?? 1;


        $data = Product::from('m_products')
            ->selectRaw("
            m_products.id,
            m_products.name,
            m_products.strength,
            m_products.is_narkotika,
            m_products.harga_jual as harga,
            a.name as unit_name,
            b.code as satuan_name,
            (
                SELECT COALESCE(SUM(CASE WHEN pq.type = 'in' THEN pq.qty WHEN pq.type = 'out' THEN -pq.qty ELSE 0 END), 0)
                FROM t_productqty as pq
                WHERE (DATE(pq.expired_date) >= CURDATE() OR pq.expired_date IS NULL)
                AND pq.location_id = ?
                AND pq.product_id = m_products.id
            ) as qty
        ", [$locationId]) // Binding $locationId masuk ke sini
            ->leftJoin('m_unitproducts as a', 'a.id', '=', 'm_products.unitfk')
            ->leftJoin('m_satuanproducts as b', 'b.id', '=', 'm_products.satuanfk')
            ->where('m_products.klienfk', $klienFk)
            ->when($q, function ($query) use ($q) {
                $query->where('m_products.name', 'LIKE', "%{$q}%");
            })
            ->limit(20)
            ->get();


        return response()->json([
            "message" => 'success',
            "data" => $data
        ], 200);
    }

    public function getPenggunaanList(Request $request)
    {
        $q = $request->q;

        $data = PenggunaanProduct::when($q, function ($query) use ($q) {
            $query->where('name', 'LIKE', "%{$q}%");
        })
            ->limit(20)
            ->get();

        return response()->json([
            "message" => 'success',
            "data" => $data
        ], 200);
    }


    protected function resolvePaymentStatus(float $totalPaid, float $price, $statbefore = null): string
    {
        if ($price <= 0) {
            return 'Belum Verifikasi';
        }

        if ($totalPaid <= 0) {
            return 'Belum Verifikasi';
        }

        // Toleransi kecil buat decimal rounding
        if ($totalPaid + 0.000001 < $price) {
            return 'Verifikasi Sebagian';
        }
        if ($statbefore == 'Lunas') {
            return 'Lunas';
        }
        return 'Verifikasi';
    }

    public function saveOrUpdateResep(Request $request, $id)
    {
        $validated = $request->validate([
            'cart' => ['required', 'array', 'min:1'],
            'cart.*.type' => ['required', \Illuminate\Validation\Rule::in(['non-racik', 'racik'])],
            'cart.*.r_num' => ['required', 'numeric'],
            'cart.*.obat_id' => ['nullable'],
            'cart.*.obat_name' => ['nullable', 'string', 'max:100'],
            'cart.*.nama_racikan' => ['nullable', 'string', 'max:255'],
            'cart.*.bentuk' => ['nullable', 'string', 'max:50'],
            'cart.*.qty' => ['nullable', 'numeric', 'min:0'],
            'cart.*.qty_buat' => ['nullable', 'numeric', 'min:0'],
            'cart.*.harga_satuan' => ['nullable', 'numeric', 'min:0'],
            'cart.*.satuan' => ['nullable', 'string', 'max:20'],
            'cart.*.penggunaan_id' => ['required', 'integer'],
            'cart.*.signa' => ['nullable', 'string', 'max:255'],
            'cart.*.subtotal' => ['required', 'numeric', 'min:0'],
            'cart.*.jasa_racik' => ['nullable', 'numeric', 'min:0'],
            'cart.*.ingredients' => ['nullable', 'array'],
            'cart.*.ingredients.*.id' => ['nullable', 'integer'],
            'cart.*.ingredients.*.name' => ['nullable', 'string', 'max:255'],
            'cart.*.ingredients.*.qty' => ['nullable', 'numeric', 'min:0'],
        ]);

        $daftarpasienId = base64_decode(base64_decode($id));
        $klien = Auth::user()->klienfk ?? 1;
        $daftarpasien = DaftarPasien::find($daftarpasienId);
        if (!$daftarpasien) {
            return response()->json([
                'success' => false,
                'message' => 'Daftar Pasien tidak ditemukan.',
                'error' => 'Daftar Pasien tidak ditemukan.',
            ], 500);
        }


        DB::beginTransaction();

        try {
            $daftarId = $daftarpasienId;
            $pasienId = $daftarpasien->pasienfk;
            $cart = $validated['cart'];

            // helper
            $num = function ($v) {
                if ($v === null || $v === '') return 0;
                $v = str_replace(',', '', (string) $v);
                return is_numeric($v) ? ($v + 0) : 0;
            };

            $nullableInt = function ($v) {
                if ($v === null || $v === '') return null;
                return (int) $v;
            };

            // 1) cek data lama aktif
            $existingResepIds = DB::table('t_reseppasien')
                ->where('pasien_id', $pasienId)
                ->where('daftarpasien_id', $daftarId)
                ->whereNull('deleted_at')
                ->pluck('id')
                ->toArray();

            $mode = empty($existingResepIds) ? 'create' : 'update';

            // 2) jika ada, hapus detail racikan + soft delete parent
            if (!empty($existingResepIds)) {
                DB::table('t_reseppasienracikan')
                    ->whereIn('reseppasien_id', $existingResepIds)
                    ->delete();

                DB::table('t_reseppasien')
                    ->whereIn('id', $existingResepIds)
                    ->update([
                        'deleted_at' => now(),
                        'updated_at' => now(),
                    ]);
            }

            // 3) insert ulang data baru
            $insertedResepIds = [];
            $racikanDetailCount = 0;

            foreach ($cart as $item) {
                $reseppasienId = DB::table('t_reseppasien')->insertGetId([
                    'klienfk'       => $klien,
                    'pasien_id'       => $pasienId,
                    'daftarpasien_id' => $daftarId,
                    'type'            => $item['type'],
                    'r_num'           => $num($item['r_num'] ?? 0),
                    'obat_id'         => $nullableInt($item['obat_id'] ?? null),
                    'obat_name'       => $item['obat_name'] ?? null,
                    'nama_racikan'    => $item['nama_racikan'] ?? null,
                    'bentuk'          => $item['bentuk'] ?? null,
                    'qty'             => $num($item['qty'] ?? 0),
                    'qty_buat'        => $num($item['qty_buat'] ?? 0),
                    'harga_satuan'    => $num($item['harga_satuan'] ?? 0),
                    'unit'            => $item['satuan'] ?? null, // payload 'satuan' -> db 'unit'
                    'penggunaan_id'   => (int) $item['penggunaan_id'],
                    'signa'           => $item['signa'] ?? null,
                    'subtotal'        => $num($item['subtotal'] ?? 0),
                    'jasa_racik'      => $num($item['jasa_racik'] ?? 0),
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);

                $insertedResepIds[] = $reseppasienId;

                if (($item['type'] ?? null) === 'racik' && !empty($item['ingredients']) && is_array($item['ingredients'])) {
                    $detailRows = [];

                    foreach ($item['ingredients'] as $ingredient) {
                        if (empty($ingredient['id'])) continue;

                        $detailRows[] = [
                            'pasien_id'       => $pasienId,
                            'daftarpasien_id' => $daftarId,
                            'reseppasien_id'  => $reseppasienId,
                            'product_id'      => (int) $ingredient['id'],
                            'product_name'    => (string) ($ingredient['name'] ?? ''),
                            'qty'             => $num($ingredient['qty'] ?? 0),
                            'created_at'      => now(),
                            'updated_at'      => now(),
                        ];
                    }

                    if (!empty($detailRows)) {
                        DB::table('t_reseppasienracikan')->insert($detailRows);
                        $racikanDetailCount += count($detailRows);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $mode === 'create' ? 'Resep berhasil disimpan' : 'Resep berhasil diupdate',
                'data' => [
                    'mode' => $mode,
                    'replaced_old_resep_count' => count($existingResepIds),
                    'resep_count' => count($insertedResepIds),
                    'racikan_detail_count' => $racikanDetailCount,
                    'reseppasien_ids' => $insertedResepIds,
                ]
            ], $mode === 'create' ? 201 : 200);
        } catch (\Throwable $e) {
            DB::rollBack();

            // opsional: log error
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Gagal simpan/update resep. Semua perubahan dibatalkan (rollback).',
                'error' => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function diagnosa($id)
    {
        $diagnosa = DiagnosaPasien::where('fk_daftarpasien', base64_decode(base64_decode($id)))->get();
        $status = DaftarPasien::select('status', 'pasienfk')->where('id', base64_decode(base64_decode($id)))->first();
        return view('pages/pemeriksaan/content/diagnosa', compact('id', 'status', 'diagnosa'));
    }

    public function saveDiagnosa(Request $request, $id)
    {
        $daftarpasienId = base64_decode(base64_decode($id));
        $klien = Auth::user()->klienfk ?? 1;

        // Kalau frontend kadang kirim "diagnosa",  fallback aman
        $items = collect($request->input('diagnosa'));

        DB::beginTransaction();

        try {
            // Normalisasi payload + filter data kosong
            $payload = $items->map(function ($i) {
                $tipe = strtoupper(trim((string) ($i['diagnosa_tipe'] ?? $i['tipe_diagnosa'] ?? '')));

                return [
                    'diagnosa_id'    => isset($i['diagnosa_id']) ? (int) $i['diagnosa_id'] : null,
                    'tipe_diagnosa'  => $tipe, // X / IX
                    'pasien_id'      => $i['pasien_id'] ?? null,
                    'dokter_id'      => $i['dokter_id'] ?? null,
                    'diagnosa_name'  => $i['diagnosa_name'] ?? ($i['tindakan_name'] ?? null), // sesuaikan field UI
                    'dokter_text'    => $i['dokter_text'] ?? null,
                    'price'          => (float) ($i['diagnosa_amount'] ?? $i['tindakan_amount'] ?? 0),
                    'qty'            => (int) ($i['qty'] ?? 1),
                ];
            })->filter(function ($i) {
                return !empty($i['diagnosa_id']) && in_array($i['tipe_diagnosa'], ['X', 'IX'], true);
            })->values();

            // ===== 1) Ambil existing rows (harus ambil ID + fk_diagnosa + tipe_diagnosa)
            $existingRows = DiagnosaPasien::query()
                ->where('klienfk', $klien)
                ->where('fk_daftarpasien', $daftarpasienId)
                ->get(['id', 'fk_diagnosa', 'tipe_diagnosa']);

            // ===== 2) Bentuk set key incoming: "X|10", "IX|10", dst
            $incomingKeySet = $payload
                ->mapWithKeys(function ($i) {
                    $key = $i['tipe_diagnosa'] . '|' . $i['diagnosa_id'];
                    return [$key => true];
                });

            // ===== 3) Tentukan row yang harus dihapus (berdasarkan composite key)
            $deleteRowIds = $existingRows
                ->filter(function ($row) use ($incomingKeySet) {
                    $rowTipe = strtoupper(trim((string) $row->tipe_diagnosa));
                    $key = $rowTipe . '|' . (int) $row->fk_diagnosa;
                    return !$incomingKeySet->has($key);
                })
                ->pluck('id')
                ->all();

            if (!empty($deleteRowIds)) {
                DiagnosaPasien::query()
                    ->where('klienfk', $klien)
                    ->where('fk_daftarpasien', $daftarpasienId)
                    ->whereIn('id', $deleteRowIds)
                    ->delete();
            }

            // ===== 4) Insert / Update incoming rows (updateOrCreate pakai composite key)
            foreach ($payload as $i) {
                $qty = max(1, (int) ($i['qty'] ?? 1));
                $price = (float) ($i['price'] ?? 0);
                $amount = $price * $qty;

                DiagnosaPasien::updateOrCreate(
                    [
                        'klienfk'         => $klien,
                        'fk_daftarpasien' => $daftarpasienId,
                        'fk_diagnosa'     => $i['diagnosa_id'],
                        'tipe_diagnosa'   => $i['tipe_diagnosa'], // pembeda utama X vs IX
                    ],
                    [
                        'fk_pasien'       => $i['pasien_id'],
                        'fk_dokter'       => $i['dokter_id'],
                        'fk_userhandler'  => Auth::id(),
                        'diagnosa'        => $i['diagnosa_name'], // ganti nama kolom kalau sebenarnya "diagnosa"
                        'dokter'          => $i['dokter_text'],
                        'price'           => $price,
                        'qty'             => $qty,
                        'amount'          => $amount,
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'message' => 'success',
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'error'  => 'Gagal menyimpan diagnosa.',
                'detail' => $e->getMessage(),
            ], 400);
        }
    }

    public function  cetaksurat($id)
    {
        $status = DaftarPasien::select(['t_daftarpasien.status', 't_daftarpasien.pasienfk', 'm_pasien.nama'])->join('m_pasien', 'm_pasien.id', 't_daftarpasien.pasienfk')->where('t_daftarpasien.id', base64_decode(base64_decode($id)))->first();
        $vitalsign = VitalSign::where('fk_daftarpasien', base64_decode(base64_decode($id)))->first();
        return view('pages/pemeriksaan/content/cetak_surat', compact('id', 'status', 'vitalsign'));
    }
}
