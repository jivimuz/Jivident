<?php

namespace App\Http\Controllers\Website;

use App\Helpers\BillingStatusHelper;
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
        $query->where('t_daftarpasien.klienfk', Auth::user()->klienfk ?? 1);

        // Total record sebelum filter
        $totalData = DaftarPasien::where('klienfk', Auth::user()->klienfk ?? 1)->count();

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
        $data =  DaftarPasien::where('id', $id)->where('klienfk', Auth::user()->klienfk ?? 1)->first();
        $payments = Payments::all();

        if ($data) {
            $payments = Payments::all();
            return view('pages/billing/detail', compact('data', 'payments', 'id'));
        }
        return redirect()->back();
    }

    public function getTagihanList(Request $request)
    {
        // Verifikasi kepemilikan cabang dulu — semua query di bawah pakai
        // daftarpasien_id ini langsung tanpa cek ulang, jadi guard di sini
        // saja sudah cukup untuk mencegah lihat tagihan cabang lain.
        if (! DaftarPasien::where('id', $request->daftarpasien_id)->where('klienfk', Auth::user()->klienfk ?? 1)->exists()) {
            return response()->json(['message' => 'error', 'error' => 'Daftar Pasien tidak ditemukan'], 404);
        }

        // Kolom yang benar adalah `biaya_registrasi` — sebelumnya select()
        // ke `biaya_pendaftaran` (kolom yang tidak pernah ada), bikin endpoint
        // ini selalu gagal dengan SQL error "Unknown column" di setiap
        // pemanggilan. Ditemukan & diperbaiki saat menambahkan tagihan lab/radiologi.
        $registrasi =  DaftarPasien::select('biaya_registrasi', 'statuspembayaran')->where('id',  $request->daftarpasien_id)->first();
        $tagihanTindakan =  TindakanPasien::where('fk_daftarpasien', $request->daftarpasien_id)->get();
        $tagihanDiagnosa =  DiagnosaPasien::where('fk_daftarpasien', $request->daftarpasien_id)->get();
        $tagihanLab = DB::table('t_labpasien')
            ->where('fk_daftarpasien', $request->daftarpasien_id)->whereNull('deleted_at')
            ->select('id', 'jenis_nama as nama', 'harga_snapshot as amount')->get();
        $tagihanRadiologi = DB::table('t_radiologipasien')
            ->where('fk_daftarpasien', $request->daftarpasien_id)->whereNull('deleted_at')
            ->select('id', 'jenis_nama as nama', 'harga_snapshot as amount')->get();
        // FK-nya `daftarpasien_id`, bukan `fk_daftarpasien` seperti tabel tagihan lain —
        // resep baru ikut ditagihkan di sini, sebelumnya subtotal-nya tidak pernah
        // masuk ke tagihan pasien sama sekali (lihat BillingStatusHelper::refresh()).
        $tagihanResep = DB::table('t_reseppasien')
            ->where('daftarpasien_id', $request->daftarpasien_id)->whereNull('deleted_at')
            ->selectRaw("id, COALESCE(obat_name, nama_racikan) as nama, subtotal as amount")->get();
        // Biaya manual admin (mis. biaya operasi) — FK-nya juga `daftarpasien_id`.
        $tagihanLain = DB::table('t_biaya_tambahan')
            ->where('daftarpasien_id', $request->daftarpasien_id)->whereNull('deleted_at')
            ->selectRaw("id, nama, jumlah as amount")->get();
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
            'tagihan_lab' => $tagihanLab,
            'tagihan_radiologi' => $tagihanRadiologi,
            'tagihan_resep' => $tagihanResep,
            'tagihan_lain' => $tagihanLain,
            'registrasi' => $registrasi ? $registrasi->biaya_registrasi : 0,
            'totalPaid' => $totalPaid ?? 0,
            'statuspembayaran' => $registrasi->statuspembayaran ?? 'Belum Verifikasi',
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
            $daftarPasien = DaftarPasien::select('noregistrasi', 'status', 'statuspembayaran')
                ->where('id', $validated['fk_daftarpasien'])
                ->where('klienfk', Auth::user()->klienfk ?? 1)
                ->first();

            if (!$daftarPasien) {
                throw new InvalidArgumentException("Daftar Pasien tidak ditemukan");
            }

            // `status == 'Selesai'` dulunya dipakai sebagai proxy "sudah
            // lunas" (pemeriksaan hanya bisa jadi Selesai setelah lunas).
            // Sekarang status Selesai menandai pemeriksaan selesai secara
            // klinis saja, independen dari pembayaran (lihat
            // PemeriksaanController::updateStatus()) — jadi guard ini cek
            // statuspembayaran langsung, supaya kasir tetap bisa mencatat
            // pembayaran atas kunjungan yang sudah selesai tapi belum lunas.
            if (($daftarPasien->statuspembayaran ?? null) == 'Lunas') {
                throw new InvalidArgumentException("Tidak bisa transaksi karena tagihan ini sudah lunas");
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

    /**
     * Penutupan manual: "Verifikasi" (tagihan sudah terbayar penuh menurut
     * sistem) vs "Lunas" (sudah dikonfirmasi/ditutup) sengaja dua langkah
     * terpisah, bukan otomatis begitu totalPaid >= totalTagihan — supaya ada
     * titik konfirmasi eksplisit sebelum kunjungan dianggap benar-benar
     * selesai dibukukan, konsisten dengan nama status "Verifikasi" itu
     * sendiri yang menyiratkan "menunggu diverifikasi", bukan "sudah pasti
     * selesai". Dihitung ulang dari BillingStatusHelper::refresh() di sini,
     * bukan percaya begitu saja pada statuspembayaran yang sudah tersimpan —
     * kalau ada tindakan/resep baru ditambahkan setelah klik terakhir di
     * halaman ini, status sekarang bisa sudah tidak lagi "Verifikasi".
     */
    public function markLunas($id)
    {
        $id = base64_decode(base64_decode($id));
        $daftarPasien = DaftarPasien::where('id', $id)->where('klienfk', Auth::user()->klienfk ?? 1)->first();

        if (!$daftarPasien) {
            return response()->json(['status' => 'error', 'message' => 'Daftar Pasien tidak ditemukan'], 404);
        }

        if ($daftarPasien->statuspembayaran === 'Lunas') {
            return response()->json(['status' => 'error', 'message' => 'Tagihan ini sudah lunas'], 400);
        }

        $status = app(BillingStatusHelper::class)->refresh($id);
        if ($status !== 'Verifikasi') {
            return response()->json([
                'status' => 'error',
                'message' => 'Tagihan belum terbayar penuh, tidak bisa ditandai lunas',
            ], 400);
        }

        $daftarPasien->update(['statuspembayaran' => 'Lunas']);
        Log::channel('user_actions')->info("User " . Auth::id() . " menandai lunas kunjungan {$daftarPasien->noregistrasi}");

        return response()->json(['status' => 'success', 'message' => 'Tagihan ditandai lunas.'], 200);
    }
}
