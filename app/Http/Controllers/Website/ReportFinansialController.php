<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportFinansialController extends Controller
{
    private const TYPECODE_LABELS = [
        'BIL' => 'Pembayaran Pasien',
        'POS' => 'Penjualan Apotek (POS)',
        'FKT' => 'Pembelian/Faktur Supplier',
        'KSR' => 'Kasir Manual',
    ];

    public function index()
    {
        return view('pages/report_finansial/index');
    }

    public function getPiutang(Request $request)
    {
        $klienfk = Auth::user()->klienfk;
        [$start, $end] = $this->resolveDateRange($request);

        $tindakanSub = DB::table('t_tindakanpasien')
            ->whereNull('deleted_at')
            ->selectRaw('fk_daftarpasien, SUM(amount) as total_tindakan')
            ->groupBy('fk_daftarpasien');

        $diagnosaSub = DB::table('t_diagnosapasien')
            ->whereNull('deleted_at')
            ->selectRaw('fk_daftarpasien, SUM(amount) as total_diagnosa')
            ->groupBy('fk_daftarpasien');

        $labSub = DB::table('t_labpasien')
            ->whereNull('deleted_at')
            ->selectRaw('fk_daftarpasien, SUM(harga_snapshot) as total_lab')
            ->groupBy('fk_daftarpasien');

        $radiologiSub = DB::table('t_radiologipasien')
            ->whereNull('deleted_at')
            ->selectRaw('fk_daftarpasien, SUM(harga_snapshot) as total_radiologi')
            ->groupBy('fk_daftarpasien');

        $paidSub = DB::table('t_transactions')
            ->whereNull('deleted_at')
            ->whereNotNull('daftarpasien_id')
            ->selectRaw("daftarpasien_id, SUM(CASE WHEN transaction_type = 'in' THEN amount WHEN transaction_type = 'out' THEN -amount ELSE 0 END) as total_paid")
            ->groupBy('daftarpasien_id');

        $rows = DB::table('t_daftarpasien as dp')
            ->leftJoin('m_pasien as p', 'p.id', 'dp.pasienfk')
            ->leftJoinSub($tindakanSub, 'ti', 'ti.fk_daftarpasien', '=', 'dp.id')
            ->leftJoinSub($diagnosaSub, 'di', 'di.fk_daftarpasien', '=', 'dp.id')
            ->leftJoinSub($labSub, 'lb', 'lb.fk_daftarpasien', '=', 'dp.id')
            ->leftJoinSub($radiologiSub, 'rd', 'rd.fk_daftarpasien', '=', 'dp.id')
            ->leftJoinSub($paidSub, 'pay', 'pay.daftarpasien_id', '=', 'dp.id')
            ->where('dp.klienfk', $klienfk)
            ->where('dp.status', '!=', 'Dibatalkan')
            ->where('dp.statuspembayaran', '!=', 'Lunas')
            ->whereDate('dp.tglregistrasi', '>=', $start)
            ->whereDate('dp.tglregistrasi', '<=', $end)
            ->selectRaw('dp.id, dp.noregistrasi, dp.tglregistrasi, dp.statuspembayaran, p.nama as pasien_nama,
                (dp.biaya_registrasi + COALESCE(ti.total_tindakan, 0) + COALESCE(di.total_diagnosa, 0) + COALESCE(lb.total_lab, 0) + COALESCE(rd.total_radiologi, 0)) as total_tagihan,
                COALESCE(pay.total_paid, 0) as total_paid')
            ->orderBy('dp.tglregistrasi')
            ->get();

        $rows->each(function ($row) {
            $row->sisa = (float) $row->total_tagihan - (float) $row->total_paid;
        });

        return response()->json(['message' => 'success', 'data' => $rows, 'total_piutang' => $rows->sum('sisa')], 200);
    }

    public function getRekapPembayaran(Request $request)
    {
        $klienfk = Auth::user()->klienfk;
        [$start, $end] = $this->resolveDateRange($request);

        $data = DB::table('t_billingtransactions as bt')
            ->leftJoin('m_payments as p', 'p.id', 'bt.fk_payment')
            ->where('bt.klienfk', $klienfk)
            ->where('bt.type', 'in')
            ->whereNull('bt.deleted_at')
            ->whereDate('bt.created_at', '>=', $start)
            ->whereDate('bt.created_at', '<=', $end)
            ->selectRaw("COALESCE(p.title, 'Lainnya') as metode, COUNT(*) as jumlah_transaksi, COALESCE(SUM(bt.amount), 0) as total")
            ->groupBy('metode')
            ->orderByDesc('total')
            ->get();

        return response()->json(['message' => 'success', 'data' => $data], 200);
    }

    public function getLabaRugi(Request $request)
    {
        $klienfk = Auth::user()->klienfk;
        [$start, $end] = $this->resolveDateRange($request);

        $rows = DB::table('t_transactions')
            ->where('klienfk', $klienfk)
            ->whereNull('deleted_at')
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $end)
            ->selectRaw('typecode, transaction_type, COALESCE(SUM(amount), 0) as total')
            ->groupBy('typecode', 'transaction_type')
            ->get();

        $breakdown = [];
        $totalIn = 0.0;
        $totalOut = 0.0;
        foreach ($rows as $row) {
            $code = $row->typecode ?: '-';
            $breakdown[$code] ??= [
                'typecode' => $code,
                'label' => self::TYPECODE_LABELS[$code] ?? $code,
                'in' => 0.0,
                'out' => 0.0,
            ];
            $breakdown[$code][$row->transaction_type] += (float) $row->total;
            if ($row->transaction_type === 'in') {
                $totalIn += (float) $row->total;
            } else {
                $totalOut += (float) $row->total;
            }
        }

        return response()->json([
            'message' => 'success',
            'data' => array_values($breakdown),
            'total_in' => $totalIn,
            'total_out' => $totalOut,
            'laba_bersih' => $totalIn - $totalOut,
        ], 200);
    }

    public function getProductOptions()
    {
        $klienfk = Auth::user()->klienfk;

        $data = DB::table('m_products')
            ->where('klienfk', $klienfk)
            ->orderBy('name')
            ->select('id', 'code', 'name')
            ->get();

        return response()->json(['message' => 'success', 'data' => $data], 200);
    }

    public function getKartuStok(Request $request)
    {
        $klienfk = Auth::user()->klienfk;
        [$start, $end] = $this->resolveDateRange($request);
        $productId = (int) $request->product_id;

        $saldoAwal = (float) DB::table('t_productqty as pq')
            ->join('m_products as p', 'p.id', 'pq.product_id')
            ->where('p.klienfk', $klienfk)
            ->where('pq.product_id', $productId)
            ->whereNull('pq.deleted_at')
            ->whereDate('pq.created_at', '<', $start)
            ->selectRaw("COALESCE(SUM(CASE WHEN pq.type = 'in' THEN pq.qty WHEN pq.type = 'out' THEN -pq.qty ELSE 0 END), 0) as saldo")
            ->value('saldo');

        $rows = DB::table('t_productqty as pq')
            ->join('m_products as p', 'p.id', 'pq.product_id')
            ->where('p.klienfk', $klienfk)
            ->where('pq.product_id', $productId)
            ->whereNull('pq.deleted_at')
            ->whereDate('pq.created_at', '>=', $start)
            ->whereDate('pq.created_at', '<=', $end)
            ->orderBy('pq.created_at')
            ->select('pq.created_at', 'pq.source', 'pq.type', 'pq.qty', 'pq.batch_no', 'pq.expired_date')
            ->get();

        $balance = $saldoAwal;
        $mutasi = [];
        foreach ($rows as $row) {
            $balance += $row->type === 'in' ? (float) $row->qty : -(float) $row->qty;
            $mutasi[] = [
                'tanggal' => $row->created_at,
                'source' => $row->source,
                'type' => $row->type,
                'qty' => (float) $row->qty,
                'batch_no' => $row->batch_no,
                'expired_date' => $row->expired_date,
                'saldo' => $balance,
            ];
        }

        return response()->json(['message' => 'success', 'saldo_awal' => $saldoAwal, 'data' => $mutasi], 200);
    }

    public function getStokOpname()
    {
        $klienfk = Auth::user()->klienfk;

        $qtySub = DB::table('t_productqty')
            ->whereNull('deleted_at')
            ->selectRaw("product_id, SUM(CASE WHEN type = 'in' THEN qty WHEN type = 'out' THEN -qty ELSE 0 END) as total_qty")
            ->groupBy('product_id');

        $data = DB::table('m_products as p')
            ->leftJoinSub($qtySub, 'q', 'q.product_id', '=', 'p.id')
            ->where('p.klienfk', $klienfk)
            ->selectRaw('p.id, p.code, p.name, p.hpp, p.harga_jual, COALESCE(q.total_qty, 0) as stok,
                (COALESCE(q.total_qty, 0) * p.hpp) as nilai_stok')
            ->orderBy('p.name')
            ->get();

        return response()->json(['message' => 'success', 'data' => $data, 'total_nilai_stok' => $data->sum('nilai_stok')], 200);
    }

    public function getPembelianSupplier(Request $request)
    {
        $klienfk = Auth::user()->klienfk;
        [$start, $end] = $this->resolveDateRange($request);

        $data = DB::table('t_receiveorders as ro')
            ->join('m_suppliers as s', 's.id', 'ro.supplier_id')
            ->where('ro.klienfk', $klienfk)
            ->whereNull('ro.deleted_at')
            ->whereDate('ro.tgl_faktur', '>=', $start)
            ->whereDate('ro.tgl_faktur', '<=', $end)
            ->selectRaw('s.name as supplier, COUNT(ro.id) as jumlah_faktur, COALESCE(SUM(ro.grand_total), 0) as total_pembelian')
            ->groupBy('s.name')
            ->orderByDesc('total_pembelian')
            ->get();

        return response()->json(['message' => 'success', 'data' => $data], 200);
    }

    private function resolveDateRange(Request $request): array
    {
        $start = $request->start_date ?? now()->startOfMonth()->toDateString();
        $end = $request->end_date ?? now()->toDateString();

        return [$start, $end];
    }
}
