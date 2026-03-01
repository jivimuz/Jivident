<?php

namespace App\Http\Controllers\Website;

use App\Helpers\TransactionHelper;
use App\Http\Controllers\Controller;
use App\Models\DaftarPasien;
use App\Models\DiagnosaPasien;
use App\Models\Payments;
use App\Models\RuangPraktek;
use App\Models\Setting;
use App\Models\TindakanPasien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class BillingController extends Controller
{
    public function index()
    {
        $ruangan = RuangPraktek::select(['m_ruangpraktek.*', 'm_users.id as dokterid', 'm_users.display_name as dokter'])
            ->join('m_users', 'm_ruangpraktek.fk_dokter', 'm_users.id')
            ->where('m_ruangpraktek.klienfk', Auth::user()->klienfk ?? 1)->get();
        return view('pages/billing/index', compact('ruangan'));
    }

    public function getList(Request $request)
    {
        $offset = $request->input('start', 0);
        $limit = $request->input('length', 10);
        $searchValue = $request->post('pasienSearch');
        $ruanganfk = $request->input('ruanganfk');
        $tglregistrasi = $request->input('tglregistrasi');
        $status_pembayaran = $request->input('status_pembayaran');

        $orderColumnIndex = $request->input('order.0.column', 0);
        // $orderDirection = $request->input('order.0.dir', 'desc');
        $orderDirection = 'desc';
        // $nik = $request->post('nik_search');

        $columns = [
            't_daftarpasien.noantrian',
            't_daftarpasien.tglregistrasi',
            'b.name as ruangan',
            'c.display_name as dokter',
            'a.norm',
            't_daftarpasien.noregistrasi',
            'a.nama',
            't_daftarpasien.statuspasien',
            't_daftarpasien.statuspembayaran',
            't_daftarpasien.status',
            't_daftarpasien.id',
            'a.nik',
        ];

        $orderColumnName = $columns[$orderColumnIndex] ?? 'id';

        // Base query
        $query = DaftarPasien::query()->select($columns);
        $query->leftJoin('m_pasien as a', 'a.id', 't_daftarpasien.pasienfk');
        $query->leftJoin('m_ruangpraktek as b', 'b.id', 't_daftarpasien.ruanganfk');
        $query->leftJoin('m_users as c', 'c.id', 't_daftarpasien.dokterfk');
        // $query->where('t_daftarpasien.status', '!=', 'Dalam Antrian');

        // Filter by NIK (kalau ada)
        if ($ruanganfk) {
            $query->where('t_daftarpasien.ruanganfk', $ruanganfk);
        }
        // Filter tanggal (support range)
        if (!empty($tglregistrasi)) {
            $tglRange = explode(' to ', $tglregistrasi);

            if (count($tglRange) === 2) {
                $query->whereBetween('t_daftarpasien.tglregistrasi', [
                    trim($tglRange[0]) . ' 00:00:00',
                    trim($tglRange[1]) . ' 23:59:59',
                ]);
            } else {
                // Jika hanya 1 tanggal
                $query->whereDate('t_daftarpasien.tglregistrasi', trim($tglregistrasi));
            }
        }

        // Filter pencarian
        if ($searchValue) {
            $query->where(function ($q) use ($searchValue) {
                $q->orWhere('a.norm', 'like', "%{$searchValue}%")
                    ->orWhere('t_daftarpasien.noregistrasi', 'like', "%{$searchValue}%")
                    ->orWhere('t_daftarpasien.statuspasien', 'like', "%{$searchValue}%")
                    ->orWhere('a.nik', 'like', "%{$searchValue}%")
                    ->orWhere('t_daftarpasien.noantrian', 'like', "%{$searchValue}%");
            });
        }

        if ($status_pembayaran && $status_pembayaran != 'All') {
            if ($status_pembayaran == "Not Yet") {
                $query->where('t_daftarpasien.statuspembayaran', '!=', 'Verifikasi')
                    ->where('t_daftarpasien.statuspembayaran', '!=', 'Lunas');
            } else {
                $query->where('t_daftarpasien.statuspembayaran', $status_pembayaran);
            }
        }
        $query->where('t_daftarpasien.status', '!=', 'Dalam Antrian');

        // Total record sebelum filter
        $totalData = DaftarPasien::count();

        // Total setelah filter
        $filteredData = $query->count();

        // Sorting dan pagination
        $query->orderBy($orderColumnName, $orderDirection)
            ->skip($offset)
            ->take($limit);

        $data = $query->get();

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $filteredData,
            'data' => $data,
        ]);
    }

    public function billingDetail($id)
    {
        $id = base64_decode(base64_decode($id));
        $data =  DaftarPasien::where('id', $id)->first();
        $payments = Payments::all();

        if ($data) {
            $payments = Payments::all();
            return view('pages/billing/detail', compact('data', 'payments', 'id'));
        }
        return redirect()->back();
    }

    public function getTagihanList(Request $request)
    {
        $registrasi =  DaftarPasien::select('biaya_pendaftaran')->where('id',  $request->daftarpasien_id)->first();
        $tagihanTindakan =  TindakanPasien::where('fk_daftarpasien', $request->daftarpasien_id)->get();
        $tagihanDiagnosa =  DiagnosaPasien::where('fk_daftarpasien', $request->daftarpasien_id)->get();
        $totalPaid = (float) DB::table('t_transactions')
            ->where('daftarpasien_id', $request->daftarpasien_id)
            ->selectRaw("COALESCE(SUM(CASE
                        WHEN transaction_type='in' THEN amount
                        WHEN transaction_type='out' THEN -amount
                        ELSE 0 END),0) as total_paid")->whereNull('deleted_at')
            ->value('total_paid');

        return response()->json([
            'message' => 'Get data sukses',
            'tagihan_tindakan' => $tagihanTindakan,
            'tagihan_diagnosa' => $tagihanDiagnosa,
            'registrasi' => $registrasi ? $registrasi->biaya_pendaftaran : 0,
            'totalPaid' => $totalPaid ?? 0,
        ], 200);
    }

    public function processPaid(Request $request)
    {
        // 1. Validasi input dari AJAX (menghindari data kosong)
        $validated = $request->validate([
            'fk_daftarpasien' => 'required|integer',
            'total_tagihan' => 'required|decimal:',
            'payment' => 'required|array',
            'selisih' => 'required',
            // 'tagihan_id' dikirimkan dari hidden input, bisa divalidasi juga
        ]);

        DB::beginTransaction();
        try {
            $transaction = new TransactionHelper();
            $daftarPasien = DaftarPasien::select('noregistrasi', 'status')
                ->where('id', $validated['fk_daftarpasien'])
                ->first();

            if (!$daftarPasien) {
                throw new InvalidArgumentException("Daftar Pasien tidak ditemukan");
            }

            if (($daftarPasien->status ?? null) == 'Selesai') { // atau payment_status, pilih salah satu
                throw new InvalidArgumentException("Tidak bisa transaksi karena sudah diselesaikan: BC");
            }
            Log::channel('user_actions')->info("User " . Auth::id() . " melakukan transaksi pembayaran {$daftarPasien->noregistrasi}");
            // Gunakan DB Transaction agar jika terjadi error di tengah jalan, data akan di-rollback
            foreach ($validated['payment'] as $i) {
                // dd($i['metode']);
                $transactionData = [
                    'transaction_type' => 'in',
                    'typecode'         => 'BIL',
                    'amount'           => $i['nominal'],
                    'notes'            =>  'Pembayaran Pasien dengan No Registrasi: ' . $daftarPasien->noregistrasi,
                    'userhandler_id'   => Auth::id(),
                    'payment_method'   => "BILLING",
                    'fk_payment'   => $i['metode'],
                    'daftarpasien_id'   => $validated['fk_daftarpasien'],

                ];

                $transaction->createTransaction($transactionData);
            }

            if ((int)$validated['selisih']  > 0) {
                $transactionData = [
                    'transaction_type' => 'out',
                    'typecode'         => 'BIL',
                    'amount'           => $validated['selisih'],
                    'notes'            => 'Kembalian Pasien dengan No Registrasi: ' . $daftarPasien->noregistrasi,
                    'userhandler_id'   => Auth::id(),
                    'payment_method'   => "BILLING",
                    'fk_payment'         => null,
                    'daftarpasien_id'   => $validated['fk_daftarpasien'],

                ];

                $transaction->createTransaction($transactionData);
            }
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Pembayaran berhasil diproses.',
            ], 200);
        } catch (\Exception $e) {
            // Batalkan semua query jika terjadi kegagalan
            DB::rollBack();
            Log::error("Error Proses Pembayaran Billing: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }
}
