<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\DaftarPasien;
use App\Models\ResepPasien;
use App\Models\ResepPasienRacikan;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrintController extends Controller
{
    public function antrianPasien($id)
    {
        $data = DaftarPasien::select([
            't_daftarpasien.id',
            't_daftarpasien.noregistrasi',
            't_daftarpasien.noantrian',
            'b.name as ruangan',
            'c.display_name as dokter',
        ])
            ->join('m_ruangpraktek as b', 'b.id', 't_daftarpasien.ruanganfk')
            ->join('m_users as c', 'c.id', 't_daftarpasien.dokterfk')
            ->where('t_daftarpasien.id', base64_decode(base64_decode($id)))->first();

        return view('print/antrian/antrian', compact('data'));
    }

    public function printResepPasien($id)
    {

        $daftarPasienId = base64_decode(base64_decode($id), true);


        if (!$daftarPasienId || !is_numeric($daftarPasienId)) {
            return response()->json([
                'success' => false,
                'message' => 'ID tidak valid'
            ], 422);
        }
        // 1. Ambil data Header Resep
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

        $company_name = Setting::whereLike('parameter', 'company_name')->first();
        $company_address = Setting::whereLike('parameter', 'company_address')->first();


        return view('print.resep.eresep', compact('data', 'patient', 'company_name', 'company_address'));
    }

    public function printSuratSakitPasien(Request $request, $id)
    {
        $pasien = DaftarPasien::select(
            'm_pasien.nama',
            'm_pasien.norm',
            'm_pasien.tgllahir',
            'm_employee.fullname as dokter_name',
            'm_employee.nik as dokter_nik',
            'm_ruangpraktek.name as ruangan',
            't_daftarpasien.tgldiperiksa',
            'm_pekerjaan.pekerjaan',
            'm_alamat.alamat'
        )
            ->selectRaw("
    COALESCE(CONCAT_WS(', ',
      NULLIF(alamat,''),
      (
        CASE
          WHEN NULLIF(rt,'') IS NOT NULL AND NULLIF(rw,'') IS NOT NULL THEN CONCAT('RT ', rt, ' / RW ', rw)
          WHEN NULLIF(rt,'') IS NOT NULL THEN CONCAT('RT ', rt)
          WHEN NULLIF(rw,'') IS NOT NULL THEN CONCAT('RW ', rw)
          ELSE NULL
        END
      ),
      NULLIF(kelurahan,''),
      NULLIF(kecamatan,''),
      NULLIF(kotakabupaten,''),
      NULLIF(provinsi,'')
    ),'') AS alamat
  ")
            ->join('m_pasien', 'm_pasien.id', 't_daftarpasien.pasienfk')
            ->leftjoin('m_employee', 'm_employee.id', 't_daftarpasien.dokterfk')
            ->leftjoin('m_ruangpraktek', 'm_ruangpraktek.id', 't_daftarpasien.ruanganfk')
            ->leftjoin('m_pekerjaan', 'm_pekerjaan.id', 'm_pasien.pekerjaanfk')
            ->leftjoin('m_alamat', 'm_alamat.id', 'm_pasien.alamatfk')
            ->where('t_daftarpasien.id', base64_decode(base64_decode($id)))->first();

        $company_name = Setting::whereLike('parameter', 'company_name')->first();
        $company_address = Setting::whereLike('parameter', 'company_address')->first();
        $company_city = Setting::whereLike('parameter', 'company_city')->first();
        $lama_istirahat = $request->lama_hari;
        $tglmulai = Carbon::parse($request->tgl_mulai ?? now())->translatedFormat('d F Y');
        $tglselesai = Carbon::parse($request->tgl_mulai ?? now())->addDays((int)$lama_istirahat ?? 2)->translatedFormat('d F Y');

        return view('print.suratpasien.surat_sakit', compact('id',  'pasien', 'company_name', 'company_address', 'company_city', 'tglmulai', 'tglselesai', 'lama_istirahat'));
    }

    public function printSuratSehatPasien(Request $request, $id)
    {
        $data = $request;
        $pasien = DaftarPasien::select(
            'm_pasien.nama',
            'm_pasien.norm',
            'm_pasien.tempatlahir',
            'm_pasien.tgllahir',
            'm_employee.fullname as dokter_name',
            'm_employee.nik as dokter_nik',
            'm_ruangpraktek.name as ruangan',
            't_daftarpasien.tgldiperiksa',
            'm_pekerjaan.pekerjaan',
            'm_jeniskelamin.jeniskelamin'
        )->selectRaw("
    COALESCE(CONCAT_WS(', ',
      NULLIF(alamat,''),
      (
        CASE
          WHEN NULLIF(rt,'') IS NOT NULL AND NULLIF(rw,'') IS NOT NULL THEN CONCAT('RT ', rt, ' / RW ', rw)
          WHEN NULLIF(rt,'') IS NOT NULL THEN CONCAT('RT ', rt)
          WHEN NULLIF(rw,'') IS NOT NULL THEN CONCAT('RW ', rw)
          ELSE NULL
        END
      ),
      NULLIF(kelurahan,''),
      NULLIF(kecamatan,''),
      NULLIF(kotakabupaten,''),
      NULLIF(provinsi,'')
    ),'') AS alamat
  ")
            ->join('m_pasien', 'm_pasien.id', 't_daftarpasien.pasienfk')
            ->leftjoin('m_employee', 'm_employee.id', 't_daftarpasien.dokterfk')
            ->leftjoin('m_ruangpraktek', 'm_ruangpraktek.id', 't_daftarpasien.ruanganfk')
            ->leftjoin('m_pekerjaan', 'm_pekerjaan.id', 'm_pasien.pekerjaanfk')
            ->leftjoin('m_jeniskelamin', 'm_jeniskelamin.id', 'm_pasien.jeniskelaminfk')
            ->leftjoin('m_alamat', 'm_alamat.id', 'm_pasien.alamatfk')
            ->where('t_daftarpasien.id', base64_decode(base64_decode($id)))->first();

        $company_name = Setting::whereLike('parameter', 'company_name')->first();
        $company_address = Setting::whereLike('parameter', 'company_address')->first();
        $company_city = Setting::whereLike('parameter', 'company_city')->first();
        $lama_istirahat = $request->lama_hari;
        $tglmulai = Carbon::parse($request->tgl_mulai ?? now())->translatedFormat('d F Y');
        $tglselesai = Carbon::parse($request->tgl_mulai ?? now())->addDays((int)$lama_istirahat ?? 2)->translatedFormat('d F Y');

        return view('print.suratpasien.surat_sehat', compact('id', 'data', 'pasien', 'company_name', 'company_address', 'company_city', 'tglmulai', 'tglselesai', 'lama_istirahat'));
    }

    public function printSuratRujukanPasien(Request $request, $id)
    {
        $data = $request;
        $pasien = DaftarPasien::select(
            'm_pasien.nama',
            'm_pasien.norm',
            'm_pasien.tempatlahir',
            'm_pasien.tgllahir',
            'm_employee.fullname as dokter_name',
            'm_employee.nik as dokter_nik',
            'm_ruangpraktek.name as ruangan',
            't_daftarpasien.tgldiperiksa',
            'm_pekerjaan.pekerjaan',
            'm_jeniskelamin.jeniskelamin'
        )->selectRaw("
    COALESCE(CONCAT_WS(', ',
      NULLIF(alamat,''),
      (
        CASE
          WHEN NULLIF(rt,'') IS NOT NULL AND NULLIF(rw,'') IS NOT NULL THEN CONCAT('RT ', rt, ' / RW ', rw)
          WHEN NULLIF(rt,'') IS NOT NULL THEN CONCAT('RT ', rt)
          WHEN NULLIF(rw,'') IS NOT NULL THEN CONCAT('RW ', rw)
          ELSE NULL
        END
      ),
      NULLIF(kelurahan,''),
      NULLIF(kecamatan,''),
      NULLIF(kotakabupaten,''),
      NULLIF(provinsi,'')
    ),'') AS alamat
  ")
            ->join('m_pasien', 'm_pasien.id', 't_daftarpasien.pasienfk')
            ->leftjoin('m_employee', 'm_employee.id', 't_daftarpasien.dokterfk')
            ->leftjoin('m_ruangpraktek', 'm_ruangpraktek.id', 't_daftarpasien.ruanganfk')
            ->leftjoin('m_pekerjaan', 'm_pekerjaan.id', 'm_pasien.pekerjaanfk')
            ->leftjoin('m_jeniskelamin', 'm_jeniskelamin.id', 'm_pasien.jeniskelaminfk')
            ->leftjoin('m_alamat', 'm_alamat.id', 'm_pasien.alamatfk')
            ->where('t_daftarpasien.id', base64_decode(base64_decode($id)))->first();

        $company_name = Setting::whereLike('parameter', 'company_name')->first();
        $company_address = Setting::whereLike('parameter', 'company_address')->first();
        $company_city = Setting::whereLike('parameter', 'company_city')->first();
        $lama_istirahat = $request->lama_hari;
        $tglmulai = Carbon::parse($request->tgl_mulai ?? now())->translatedFormat('d F Y');
        $tglselesai = Carbon::parse($request->tgl_mulai ?? now())->addDays((int)$lama_istirahat ?? 2)->translatedFormat('d F Y');

        return view('print.suratpasien.surat_rujukan', compact('id', 'data', 'pasien', 'company_name', 'company_address', 'company_city', 'tglmulai', 'tglselesai', 'lama_istirahat'));
    }
}
