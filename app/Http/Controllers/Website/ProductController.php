<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategories;
use App\Models\ProductLogs;
use App\Models\SatuanProduct;
use App\Models\UnitProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $categories = ProductCategories::all();
        return view('pages/product/index', compact('categories'));
    }


    public function getList(Request $request)
    {
        // 1. Definisikan Kolom untuk Sorting
        // Sesuaikan index dengan urutan kolom di Javascript DataTables
        $columns = [
            0 => 'm_products.name',
            1 => 'a.name',
            2 => 'm_products.is_stockneeded',
            3 => 'm_products.id',
        ];

        // 2. Base Query
        // Menggunakan alias 'a' untuk kategori dan memaksa tabel utama jadi 'm_products'
        $query = Product::from('m_products')
            ->leftJoin('m_productcategories as a', 'm_products.kategorifk', '=', 'a.id')
            ->leftJoin('m_satuanproducts as b', 'm_products.satuanfk', '=', 'b.id')
            ->leftJoin('m_unitproducts as c', 'm_products.unitfk', '=', 'c.id')
            ->select(
                'm_products.*',
                'a.name as category_name',
                'b.code as satuan_name',
                'c.name as unit_name',
            );

        // 3. Hitung Total Data (Sebelum filtering search)
        $totalData = $query->count();
        $totalFiltered = $totalData;

        // 4. Searching & Input Handling
        // FIX: Cast ke (int) dan beri nilai default untuk mencegah error SQL
        $limit = (int) $request->input('length', 10);
        $start = (int) $request->input('start', 0);
        $search = $request->input('search.value');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('m_products.name', 'LIKE', "%{$search}%")
                    ->orWhere('a.name', 'LIKE', "%{$search}%");
            });

            // Hitung ulang total setelah filter search
            $totalFiltered = $query->count();
        }

        // 5. Sorting (Order By)
        $orderColumnIndex = $request->input('order.0.column');
        $orderDir = $request->input('order.0.dir');

        if (isset($columns[$orderColumnIndex])) {
            $query->orderBy($columns[$orderColumnIndex], $orderDir);
        } else {
            $query->orderBy('m_products.id', 'desc');
        }

        // 6. Pagination (Limit & Offset)
        // FIX: Pastikan limit > 0. Jika -1 (show all), skip limit/offset.
        if ($limit > 0) {
            $query->offset($start)->limit($limit);
        }

        // 7. Ambil Data
        $dataRaw = $query->get();

        // 8. Formatting Data


        // 9. Return JSON
        return response()->json([
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $dataRaw
        ]);
    }
    public function add()
    {
        $categories = ProductCategories::all();
        $satuan = SatuanProduct::all();
        $unit = UnitProduct::all();
        return view('pages/product/add', compact('categories', 'unit', 'satuan'));
    }

    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'code' => 'required|string|max:50|unique:m_products,code',
            'name' => 'required|string|max:255',
            'strength' => 'required|numeric',
            'kategorifk' => 'nullable|integer',
            'unitfk' => 'required|integer',
            'satuanfk' => 'nullable|integer',
            'harga_beli' => 'nullable|numeric',
            'hpp' => 'nullable|numeric',
            'harga_jual' => 'nullable|numeric',
        ], [
            'code.unique' => 'Kode obat sudah digunakan, silakan masukkan kode lain.',
            'code.required' => 'Kode obat wajib diisi.',
            'name.required' => 'Nama obat wajib diisi.',
        ]);

        DB::beginTransaction();

        try {
            // 2. Insert ke tabel m_products
            DB::table('m_products')->insert([
                'klienfk' => 1, // Default sesuai skema
                'code' => $request->code,
                'name' => $request->name,
                'strength' => $request->strength,
                'satuanfk' => $request->satuanfk,
                'unitfk' => $request->unitfk,
                'kategorifk' => $request->kategorifk,

                // Checkbox: jika di-check nilainya ada (true/1), jika tidak default 0
                'is_fornas' => $request->has('is_fornas') ? 1 : 0,
                'is_generik' => $request->has('is_generik') ? 1 : 0,
                'is_antibiotik' => $request->has('is_antibiotik') ? 1 : 0,
                'is_psikotropika' => $request->has('is_psikotropika') ? 1 : 0,
                'is_narkotika' => $request->has('is_narkotika') ? 1 : 0,
                'is_kronis' => $request->has('is_kronis') ? 1 : 0,

                // Keuangan & Harga (Default 0 jika kosong)
                'harga_beli' => $request->harga_beli ?? 0,
                'hpp' => $request->hpp ?? 0,
                'harga_jual' => $request->harga_jual ?? 0,

                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Data master produk/obat berhasil ditambahkan.'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error Insert Produk Farmasi: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem saat menyimpan data.'
            ], 500);
        }
    }

    public function edit($id)
    {
        $data = Product::findOrFail(base64_decode(base64_decode($id)));
        $categories = ProductCategories::all();
        $satuan = SatuanProduct::all();
        $unit = UnitProduct::all();
        return view('pages/product/edit', compact('data', 'categories', 'unit', 'satuan', 'id'));
    }

    public function update(Request $request, $id)
    {
        // 1. Validasi Input
        $request->validate([
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'strength' => 'required|numeric',
            'kategorifk' => 'nullable|integer',
            'unitfk' => 'required|integer',
            'satuanfk' => 'nullable|integer',
            'harga_beli' => 'nullable|numeric',
            'hpp' => 'nullable|numeric',
            'harga_jual' => 'nullable|numeric',
        ], [
            'code.unique' => 'Kode obat sudah digunakan, silakan masukkan kode lain.',
            'code.required' => 'Kode obat wajib diisi.',
            'name.required' => 'Nama obat wajib diisi.',
        ]);

        DB::beginTransaction();
        Product::findOrFail(base64_decode(base64_decode($id)));

        try {
            // 2. Insert ke tabel m_products
            DB::table('m_products')->where('id', base64_decode(base64_decode($id)))->update([
                'code' => $request->code,
                'name' => $request->name,
                'strength' => $request->strength,
                'satuanfk' => $request->satuanfk,
                'unitfk' => $request->unitfk,
                'kategorifk' => $request->kategorifk,

                // Checkbox: jika di-check nilainya ada (true/1), jika tidak default 0
                'is_fornas' => $request->has('is_fornas') ? 1 : 0,
                'is_generik' => $request->has('is_generik') ? 1 : 0,
                'is_antibiotik' => $request->has('is_antibiotik') ? 1 : 0,
                'is_psikotropika' => $request->has('is_psikotropika') ? 1 : 0,
                'is_narkotika' => $request->has('is_narkotika') ? 1 : 0,
                'is_kronis' => $request->has('is_kronis') ? 1 : 0,

                // Keuangan & Harga (Default 0 jika kosong)
                'harga_beli' => $request->harga_beli ?? 0,
                'hpp' => $request->hpp ?? 0,
                'harga_jual' => $request->harga_jual ?? 0,

                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Data master produk/obat berhasil ditambahkan.'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error Insert Produk Farmasi: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem saat menyimpan data.'
            ], 500);
        }
    }



    public function destroy($id)
    {
        $product = Product::findOrFail(base64_decode(base64_decode($id)));

        // 🗑️ hapus foto kalau ada
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
