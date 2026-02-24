<?php

namespace App\Http\Controllers\Website;

use App\Helpers\ProductQtyHelper;
use App\Helpers\TransactionHelper;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductQTY;
use App\Models\Supplier;
use App\Models\UnitProduct;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ReceiveOrderController extends Controller
{
    public function index()
    {
        return view('pages/receive_order/index');
    }

    public function getData(Request $request)
    {
        // Mapping index kolom DataTables -> nama kolom DB
        $columns = [
            0 => 't_receiveorders.no_faktur',
            1 => 't_receiveorders.tgl_faktur',
            2 => 'm_suppliers.name',
            3 => 't_receiveorders.grand_total',
            4 => 't_receiveorders.id',
        ];

        $baseQuery = DB::table('t_receiveorders')
            ->leftJoin('m_suppliers', 't_receiveorders.supplier_id', '=', 'm_suppliers.id')
            ->whereNull('t_receiveorders.deleted_at');

        // Total data sebelum filter
        $recordsTotal = (clone $baseQuery)->count('t_receiveorders.id');

        // Filter custom (dari form kamu)
        if ($request->filled('no_faktur')) {
            $baseQuery->where('t_receiveorders.no_faktur', 'like', '%' . trim($request->no_faktur) . '%');
        }

        // Search global DataTables
        $searchValue = $request->input('search.value');
        if (!empty($searchValue)) {
            $baseQuery->where(function ($q) use ($searchValue) {
                $q->where('t_receiveorders.no_faktur', 'like', "%{$searchValue}%")
                    ->orWhere('m_suppliers.name', 'like', "%{$searchValue}%")
                    ->orWhere('t_receiveorders.grand_total', 'like', "%{$searchValue}%");
            });
        }

        // Total data setelah filter
        $recordsFiltered = (clone $baseQuery)->count('t_receiveorders.id');

        // Sorting
        $orderColumnIndex = (int) $request->input('order.0.column', 4);
        $orderDir = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $orderColumn = $columns[$orderColumnIndex] ?? 't_receiveorders.created_at';

        // Paging
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        $query = $baseQuery
            ->select(
                't_receiveorders.id',
                't_receiveorders.no_faktur',
                't_receiveorders.tgl_faktur',
                't_receiveorders.supplier_id',
                't_receiveorders.grand_total',
                't_receiveorders.is_processed',
                't_receiveorders.created_at',
                'm_suppliers.name as supplier_name'
            )
            ->orderBy($orderColumn, $orderDir);

        if ($length !== -1) { // -1 = all (DataTables)
            $query->offset($start)->limit($length);
        }

        $rows = $query->get();

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $rows,
        ]);
    }

    public function create()
    {
        return view('pages/receive_order/add');
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|integer',
            'no_faktur' => 'required|string|max:100',
            'tgl_faktur' => 'required|date',
            'total_bruto' => 'required|numeric',
            'diskon_global' => 'required|numeric',
            'ppn_persen' => 'required|numeric',
            'grand_total' => 'required|numeric',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|integer',
            'batch_no.*' => 'required|string|max:50',
            'expired_date.*' => 'required',
            'qty.*' => 'required|numeric|min:1',
            'harga_beli.*' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        $now    = CarbonImmutable::now();

        $rand = Str::upper(Str::random(8));
        $noRO   = "RO-{$now->format('Ymd')}{$rand}{$now->format('His')}";

        try {
            $receiveOrderId = DB::table('t_receiveorders')->insertGetId([
                'klienfk' => Auth::user()->klienfk ?? 1,
                'no_receipt' => $noRO,
                'supplier_id' => $request->supplier_id,
                'no_faktur' => $request->no_faktur,
                'tgl_faktur' => $request->tgl_faktur,
                'tgl_jatuh_tempo' => $request->tgl_jatuh_tempo,
                'no_po' => $request->no_po,
                'keterangan' => $request->keterangan,
                'total_bruto' => $request->total_bruto,
                'diskon_global' => $request->diskon_global,
                'ppn_persen' => $request->ppn_persen,
                'grand_total' => $request->grand_total,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $details = [];

            foreach ($request->product_id as $index => $productId) {
                $expiredDate = $request->expired_date[$index] . '-01';

                $details[] = [
                    'klienfk' => Auth::user()->klienfk ?? 1,
                    'receiveorder_id' => $receiveOrderId,
                    'product_id' => $productId,
                    'batch_no' => $request->batch_no[$index],
                    'expired_date' => $expiredDate,
                    'qty' => $request->qty[$index],
                    'unit_id' => $request->unit_id[$index] ?? null,
                    'harga_beli' => $request->harga_beli[$index] ?? 0,
                    'diskon_persen' => $request->diskon_persen[$index] ?? 0,
                    'subtotal' => $request->subtotal[$index] ?? 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Logic Insert Stok Obat
                // ProductQTY::insert([
                //     'receipt_id' => $receiveOrderId,
                //     'product_id' => $productId,
                //     'location_id' => 1,
                //     'expired_date' => $expiredDate,
                //     'qty' => $request->qty[$index],
                //     'type' => 'in',
                //     'source' => 'receive',
                // ]);

                // $this->updateProductPrices($productId, $request->harga_beli[$index] ?? 0, $request->ppn_persen ?? 0);
            }

            DB::table('t_receiveorderdetails')->insert($details);


            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Data penerimaan barang dan faktur berhasil disimpan.'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error Simpan Receive Order: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem saat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan Form Edit Receive Order
     */
    public function edit($id)
    {
        $receiveOrder = DB::table('t_receiveorders')
            ->select([
                't_receiveorders.*',
                'a.name as supplier'
            ])
            ->leftJoin('m_suppliers as a', 'a.id', 't_receiveorders.supplier_id')
            ->where('t_receiveorders.id', base64_decode(base64_decode($id)))->first();
        if (!$receiveOrder) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }
        if ($receiveOrder->is_processed) {
            return redirect(url('receive_order/show/' . $id));
        }

        // Dapatkan details dan join nama product untuk pre-fill select
        $details = DB::table('t_receiveorderdetails')
            ->leftJoin('m_products', 't_receiveorderdetails.product_id', '=', 'm_products.id')
            ->leftJoin('m_unitproducts', 't_receiveorderdetails.unit_id', '=', 'm_unitproducts.id')
            ->select('t_receiveorderdetails.*', 'm_products.name as product_name', 'm_unitproducts.name as unit_name')
            ->where('receiveorder_id', base64_decode(base64_decode($id)))
            ->get();

        return view('pages/receive_order/edit', compact('receiveOrder', 'details'));
    }

    public function show($id)
    {
        $receiveOrder = DB::table('t_receiveorders')
            ->select([
                't_receiveorders.*',
                'a.name as supplier'
            ])
            ->leftJoin('m_suppliers as a', 'a.id', 't_receiveorders.supplier_id')
            ->where('t_receiveorders.id', base64_decode(base64_decode($id)))->first();
        if (!$receiveOrder) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        // Dapatkan details dan join nama product untuk pre-fill select
        $details = DB::table('t_receiveorderdetails')
            ->leftJoin('m_products', 't_receiveorderdetails.product_id', '=', 'm_products.id')
            ->leftJoin('m_unitproducts', 't_receiveorderdetails.unit_id', '=', 'm_unitproducts.id')
            ->select('t_receiveorderdetails.*', 'm_products.name as product_name', 'm_unitproducts.name as unit_name')
            ->where('receiveorder_id', base64_decode(base64_decode($id)))
            ->get();

        return view('pages/receive_order/show', compact('receiveOrder', 'details'));
    }


    /**
     * Mengupdate Data (Wipe & Replace Strategy untuk Stock dan Details)
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'supplier_id' => 'required|integer',
            'no_faktur' => 'required|string|max:100',
            'tgl_faktur' => 'required|date',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|integer',
            'qty.*' => 'required|numeric|min:1',
        ]);
        $ro = DB::table('t_receiveorders')->select('is_processed')->where('id', base64_decode(base64_decode($id)))->first();
        if (!$ro) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }
        if ($ro->is_processed == null || $ro->is_processed == '') {
            DB::beginTransaction();
            try {
                // 1. Update Header
                DB::table('t_receiveorders')->where('id', base64_decode(base64_decode($id)))->update([
                    'supplier_id' => $request->supplier_id,
                    'no_faktur' => $request->no_faktur,
                    'tgl_faktur' => $request->tgl_faktur,
                    'tgl_jatuh_tempo' => $request->tgl_jatuh_tempo,
                    'no_po' => $request->no_po,
                    'keterangan' => $request->keterangan,
                    'total_bruto' => $request->total_bruto,
                    'diskon_global' => $request->diskon_global,
                    'ppn_persen' => $request->ppn_persen,
                    'grand_total' => $request->grand_total,
                    'updated_at' => now(),
                ]);

                // 2. WIPE (Hapus) Stok Lama & Detail Lama
                // Cara ini memastikan tidak ada residu stok/detail yang nyangkut jika baris dihapus dari form
                ProductQTY::where('receipt_id', base64_decode(base64_decode($id)))->where('type', 'in')->delete();
                DB::table('t_receiveorderdetails')->where('receiveorder_id', base64_decode(base64_decode($id)))->delete();

                // 3. REPLACE (Insert ulang) Detail dan Stok Baru
                $details = [];
                foreach ($request->product_id as $index => $productId) {
                    $expiredDate = $request->expired_date[$index] . '-01';

                    $details[] = [
                        'klienfk' => Auth::user()->klienfk ?? 1,
                        'receiveorder_id' => base64_decode(base64_decode($id)),
                        'product_id' => $productId,
                        'batch_no' => $request->batch_no[$index],
                        'expired_date' => $expiredDate,
                        'qty' => $request->qty[$index],
                        'unit_id' => $request->unit_id[$index] ?? null,
                        'harga_beli' => $request->harga_beli[$index] ?? 0,
                        'diskon_persen' => $request->diskon_persen[$index] ?? 0,
                        'subtotal' => $request->subtotal[$index] ?? 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    // Re-insert Stok Baru
                    // ProductQTY::insert([
                    //     'receipt_id' => base64_decode(base64_decode($id)),
                    //     'product_id' => $productId,
                    //     'location_id' => 1,
                    //     'expired_date' => $expiredDate,
                    //     'qty' => $request->qty[$index],
                    //     'type' => 'in',
                    //     'source' => 'receive',
                    // ]);

                    // // Update harga produk (HPP / Jual)
                    // $this->updateProductPrices($productId, $request->harga_beli[$index] ?? 0, $request->ppn_persen ?? 0);
                }

                DB::table('t_receiveorderdetails')->insert($details);

                // Jika Transaction Helper memiliki update logic, panggil di sini
                // (Abaikan jika transaksi keuangan dihandle secara statis)

                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Data penerimaan barang berhasil diperbarui.'
                ], 200);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error Update Receive Order: ' . $e->getMessage());

                return response()->json([
                    'status' => 'error',
                    'message' => 'Terjadi kesalahan sistem saat mengupdate data: ' . $e->getMessage()
                ], 500);
            }
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Data Sudah Di Proses.'
        ], 404);
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            // 1. Validasi apakah data transaksi masih ada
            $ro = DB::table('t_receiveorders')->where('id', base64_decode(base64_decode($id)))->first();
            if (!$ro) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan.'
                ], 404);
            }

            if ($ro->is_processed == null || $ro->is_processed == '') {
                // 2. Soft Delete Header Transaksi Utama
                DB::table('t_receiveorders')->where('id', base64_decode(base64_decode($id)))->update(['deleted_at' => now()]);

                // 3. Soft Delete pada Detail Item
                DB::table('t_receiveorderdetails')->where('receiveorder_id', base64_decode(base64_decode($id)))->update(['deleted_at' => now()]);

                // 4. HARD DELETE (Wipe Out) Stok di ProductQTY
                // Sangat penting agar kuantitas obat di inventaris kembali berkurang
                ProductQTY::where('receipt_id', base64_decode(base64_decode($id)))->where('type', 'in')->delete();


                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Data penerimaan barang berhasil dihapus dan riwayat stok telah dibatalkan.'
                ], 200);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Data Sudah Di Proses.'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error Delete Receive Order: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem saat menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function approve($id)
    {
        DB::beginTransaction();
        try {
            // 1. Validasi apakah data transaksi masih ada
            $ro = DB::table('t_receiveorders')->where('id', base64_decode(base64_decode($id)))->first();
            if (!$ro) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan.'
                ], 404);
            }
            if ($ro->is_processed == null || $ro->is_processed == '' || $ro->is_processed == 0) {

                $transaction = new TransactionHelper();


                $transactionData = [
                    'transaction_type' => 'out',
                    'typecode'         => 'FKT',
                    'amount'           => $ro->grand_total,
                    'notes'            => "Pembayaran Faktur Supplier No Receipt: {$ro->no_receipt}",
                    'userhandler_id'   => Auth::id(),
                    'payment_method'   => "SYSTEM",
                ];

                $transaction->createTransaction($transactionData);

                $helper = app(ProductQtyHelper::class);
                $res = $helper->processReceiveOrder(base64_decode(base64_decode($id)), 1);


                DB::commit();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Data penerimaan barang berhasil diapprove dan transaksi out dilakukan.'
                ], 200);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Data Sudah Di Proses.'
            ], 400);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error Approve Receive Order: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem saat mengapprove data: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Logic dipisah agar rapih (Mengupdate Harga HPP & Jual Obat)
     */

    public function getSupplierList(Request $request)
    {
        $q = $request->q;

        $data = Supplier::where('klienfk', Auth::user()->klienfk ?? 1)
            ->when($q, function ($query) use ($q) {
                $query->where('a.name', 'LIKE', "%{$q}%");
            })
            ->limit(20)
            ->get();

        return response()->json([
            "message" => 'success',
            "data" => $data
        ], 200);
    }

    public function getProductList(Request $request)
    {
        $q = $request->q;

        $data = Product::where('klienfk', Auth::user()->klienfk ?? 1)
            ->when($q, function ($query) use ($q) {
                $query->where('a.name', 'LIKE', "%{$q}%");
            })
            ->limit(20)
            ->get();

        return response()->json([
            "message" => 'success',
            "data" => $data
        ], 200);
    }
    public function getUnitList(Request $request)
    {
        $q = $request->q;

        $data = UnitProduct::when($q, function ($query) use ($q) {
            $query->where('a.name', 'LIKE', "%{$q}%");
        })
            ->limit(20)
            ->get();

        return response()->json([
            "message" => 'success',
            "data" => $data
        ], 200);
    }
}
