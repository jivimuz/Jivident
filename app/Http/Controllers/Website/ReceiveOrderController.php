<?php

namespace App\Http\Controllers\Website;

use App\Helpers\TransactionHelper;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductQTY;
use App\Models\Supplier;
use App\Models\UnitProduct;
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
        $query = DB::table('t_receiveorders')
            ->leftJoin('m_suppliers', 't_receiveorders.supplier_id', '=', 'm_suppliers.id')
            ->select('t_receiveorders.*', 'm_suppliers.name as supplier_name')
            ->whereNull('t_receiveorders.deleted_at')
            ->orderBy('t_receiveorders.created_at', 'desc');

        if ($request->no_faktur) {
            $query->where('t_receiveorders.no_faktur', 'like', '%' . $request->no_faktur . '%');
        }

        $data = $query->limit(100)->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'no_faktur' => $item->no_faktur,
                'tgl_faktur' => $item->tgl_faktur,
                'supplier_name' => $item->supplier_name ?? 'Supplier ID: ' . $item->supplier_id,
                'grand_total' => $item->grand_total,
            ];
        });

        return response()->json(['data' => $data]);
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
                ProductQTY::insert([
                    'receipt_id' => $receiveOrderId,
                    'product_id' => $productId,
                    'location_id' => 1,
                    'expired_date' => $expiredDate,
                    'qty' => $request->qty[$index],
                    'type' => 'in',
                ]);

                $this->updateProductPrices($productId, $request->harga_beli[$index] ?? 0, $request->ppn_persen ?? 0);
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
        $receiveOrder = DB::table('t_receiveorders')->where('id', base64_decode(base64_decode($id)))->first();
        if (!$receiveOrder) {
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }

        // Dapatkan details dan join nama product untuk pre-fill select
        $details = DB::table('t_receiveorderdetails')
            ->leftJoin('m_products', 't_receiveorderdetails.product_id', '=', 'm_products.id')
            ->select('t_receiveorderdetails.*', 'm_products.name as product_name')
            ->where('receiveorder_id', base64_decode(base64_decode($id)))
            ->get();

        return view('pages.receive_order.edit', compact('receiveOrder', 'details'));
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
                ProductQTY::insert([
                    'receipt_id' => base64_decode(base64_decode($id)),
                    'product_id' => $productId,
                    'location_id' => 1,
                    'expired_date' => $expiredDate,
                    'qty' => $request->qty[$index],
                    'type' => 'in',
                ]);

                // Update harga produk (HPP / Jual)
                $this->updateProductPrices($productId, $request->harga_beli[$index] ?? 0, $request->ppn_persen ?? 0);
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
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error Delete Receive Order: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem saat menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Logic dipisah agar rapih (Mengupdate Harga HPP & Jual Obat)
     */
    private function updateProductPrices($productId, $hargaBeli, $ppnPersen)
    {
        $hargaBeli = (float) $hargaBeli;
        $ppnPersen = (float) $ppnPersen;

        $ppnNominal = $hargaBeli * ($ppnPersen / 100);
        $hppMinimal = $hargaBeli + $ppnNominal;
        $hargaJualMinimal = $hppMinimal * 1.5;

        $dataproduct = DB::table('m_products')->where('id', $productId)->first();

        if ($dataproduct) {
            $updateData = ['harga_beli' => $hargaBeli];

            if ((float) ($dataproduct->hpp ?? 0) < $hppMinimal) {
                $updateData['hpp'] = $hppMinimal;
            }

            if ((float) ($dataproduct->harga_jual ?? 0) < $hargaJualMinimal) {
                $updateData['harga_jual'] = $hargaJualMinimal;
            }

            DB::table('m_products')->where('id', $productId)->update($updateData);
        }
    }

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
