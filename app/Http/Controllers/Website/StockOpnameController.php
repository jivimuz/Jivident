<?php

namespace App\Http\Controllers\Website;

use App\Helpers\ProductQtyHelper;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductQTY;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class StockOpnameController extends Controller
{
    /**
     * Satu-satunya lokasi stok yang dipakai di seluruh app saat ini
     * (`m_productlocations` baru pernah diseed satu baris, "Warehouse", dan
     * tidak ada controller lain — termasuk ReceiveOrderController/POS — yang
     * benar-benar mengekspos pemilih lokasi ke user, semuanya hardcode ke id
     * ini). Opname ikut konvensi yang sama alih-alih membangun UI
     * multi-lokasi yang belum ada presedennya di tempat lain.
     */
    private const DEFAULT_LOCATION_ID = 1;

    public function index()
    {
        return view('pages/stock_opname/index');
    }

    public function getList(Request $request)
    {
        $klienfk = Auth::user()->klienfk ?? 1;

        $columns = [
            0 => 'm_products.code',
            1 => 'm_products.name',
            2 => 'a.name',
        ];

        $query = Product::from('m_products')
            ->leftJoin('m_productcategories as a', 'm_products.kategorifk', '=', 'a.id')
            ->leftJoin('m_satuanproducts as b', 'm_products.satuanfk', '=', 'b.id')
            ->leftJoin('m_unitproducts as c', 'm_products.unitfk', '=', 'c.id')
            ->where('m_products.klienfk', $klienfk)
            ->select(
                'm_products.id',
                'm_products.code',
                'm_products.name',
                'a.name as category_name',
                'b.code as satuan_name',
                'c.name as unit_name',
            );

        $totalData = $query->count();
        $totalFiltered = $totalData;

        $limit = (int) $request->input('length', 10);
        $start = (int) $request->input('start', 0);
        $search = $request->input('search.value');

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('m_products.name', 'like', "%{$search}%")
                    ->orWhere('m_products.code', 'like', "%{$search}%");
            });
            $totalFiltered = $query->count();
        }

        $orderColumnIndex = $request->input('order.0.column');
        $orderDir = $request->input('order.0.dir');
        if (isset($columns[$orderColumnIndex])) {
            $query->orderBy($columns[$orderColumnIndex], $orderDir);
        } else {
            $query->orderBy('m_products.name', 'asc');
        }

        if ($limit > 0) {
            $query->offset($start)->limit($limit);
        }

        $rows = $query->get();

        $balances = app(ProductQtyHelper::class)
            ->getBalancesByProducts($rows->pluck('id')->all(), self::DEFAULT_LOCATION_ID);

        $rows->transform(function ($row) use ($balances) {
            $row->qty_sistem = $balances[(int) $row->id] ?? 0;

            return $row;
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $rows,
        ]);
    }

    /**
     * Koreksi stok total (tanpa lot) berdasarkan hasil hitung fisik — insert
     * mutasi IN/OUT lewat ProductQtyHelper::stockOpnameAdjustTotal(), bukan
     * meng-overwrite m_products.qty langsung, supaya selisihnya tetap
     * tercatat sebagai riwayat mutasi seperti stok masuk/keluar lainnya.
     */
    public function adjust(Request $request)
    {
        $klienfk = Auth::user()->klienfk ?? 1;

        $validated = $request->validate([
            'product_id' => 'required|integer',
            'qty_fisik' => 'required|numeric|min:0',
        ]);

        $product = Product::where('klienfk', $klienfk)->find($validated['product_id']);
        if (! $product) {
            return response()->json(['message' => 'error', 'error' => 'Produk tidak ditemukan'], 404);
        }

        try {
            $res = app(ProductQtyHelper::class)->stockOpnameAdjustTotal(
                productId: (int) $validated['product_id'],
                locationId: self::DEFAULT_LOCATION_ID,
                qtyFisik: (float) $validated['qty_fisik'],
            );

            return response()->json(['message' => 'success'] + $res, 200);
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => 'error', 'error' => $e->getMessage()], 400);
        }
    }

    /**
     * Riwayat koreksi opname terakhir, buat jejak audit "siapa/kapan selisih
     * stok dikoreksi" — scoped lewat join ke m_products biar tidak
     * menampilkan opname cabang lain.
     */
    public function getHistory()
    {
        $klienfk = Auth::user()->klienfk ?? 1;

        $rows = ProductQTY::query()
            ->join('m_products', 'm_products.id', 't_productqty.product_id')
            ->where('t_productqty.source', 'opname')
            ->where('m_products.klienfk', $klienfk)
            ->select([
                't_productqty.id',
                't_productqty.qty',
                't_productqty.type',
                't_productqty.created_at',
                'm_products.name as product_name',
                'm_products.code as product_code',
            ])
            ->orderByDesc('t_productqty.created_at')
            ->limit(50)
            ->get();

        return response()->json(['message' => 'success', 'data' => $rows], 200);
    }
}
