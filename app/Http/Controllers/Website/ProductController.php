<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategories;
use App\Models\SatuanProduct;
use App\Models\UnitProduct;
use Carbon\Carbon;
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
        $qtySub = DB::table('t_productqty as pq')
            ->selectRaw("
        pq.product_id,
        COALESCE(SUM(
            CASE
                WHEN pq.type = 'in' THEN pq.qty
                WHEN pq.type = 'out' THEN -pq.qty
                ELSE 0
            END
        ), 0) as total_qty
    ")->whereNull('deleted_at')
            ->whereDate('expired_date', '>', Carbon::now()->format('Y-m-d'))
            ->groupBy('pq.product_id');

        $query = Product::from('m_products')
            ->leftJoin('m_productcategories as a', 'm_products.kategorifk', '=', 'a.id')
            ->leftJoin('m_satuanproducts as b', 'm_products.satuanfk', '=', 'b.id')
            ->leftJoin('m_unitproducts as c', 'm_products.unitfk', '=', 'c.id')
            ->leftJoinSub($qtySub, 'q', function ($join) {
                $join->on('q.product_id', '=', 'm_products.id');
            })
            ->select(
                'm_products.*',
                'a.name as category_name',
                'b.code as satuan_name',
                'c.name as unit_name',
                DB::raw('COALESCE(q.total_qty, 0) as total_qty')
            );

        // 3. Hitung Total Data (Sebelum filtering search)
        $totalData = $query->count();
        $totalFiltered = $totalData;

        // 4. Searching & Input Handling
        // FIX: Cast ke (int) dan beri nilai default untuk mencegah error SQL
        $limit = (int) $request->input('length', 10);
        $start = (int) $request->input('start', 0);
        $search = $request->input('search.value');

        if (! empty($search)) {
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
            'draw' => intval($request->input('draw')),
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => intval($totalFiltered),
            'data' => $dataRaw,
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
                'klienfk' => Auth::user()->klienfk ?? 1, // Default sesuai skema
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
                'message' => 'Data master produk/obat berhasil ditambahkan.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error Insert Produk Farmasi: '.$e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem saat menyimpan data.',
            ], 500);
        }
    }

    public function edit($id)
    {
        $data = Product::where('klienfk', Auth::user()->klienfk ?? 1)->findOrFail(base64_decode(base64_decode($id)));
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
        $klienfk = Auth::user()->klienfk ?? 1;
        Product::where('klienfk', $klienfk)->findOrFail(base64_decode(base64_decode($id)));

        try {
            // 2. Insert ke tabel m_products
            DB::table('m_products')->where('id', base64_decode(base64_decode($id)))->where('klienfk', $klienfk)->update([
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
                'message' => 'Data master produk/obat berhasil ditambahkan.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error Insert Produk Farmasi: '.$e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem saat menyimpan data.',
            ], 500);
        }
    }

    public function destroy($id)
    {
        $product = Product::where('klienfk', Auth::user()->klienfk ?? 1)->findOrFail(base64_decode(base64_decode($id)));

        // 🗑️ hapus foto kalau ada
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json(['message' => 'Deleted']);
    }

    private const IMPORT_HEADER = [
        'code', 'name', 'strength', 'satuan', 'unit', 'kategori',
        'harga_beli', 'hpp', 'harga_jual',
        'is_fornas', 'is_generik', 'is_antibiotik', 'is_psikotropika', 'is_narkotika', 'is_kronis',
    ];

    public function downloadImportTemplate()
    {
        $lines = [
            implode(',', self::IMPORT_HEADER),
            'PCT001,Paracetamol 500mg,500,Strip,Tablet,Analgesik,8000,10000,15000,1,1,0,0,0,0',
        ];

        return response(implode("\n", $lines))
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="template-import-produk.csv"');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $handle = fopen($request->file('file')->getRealPath(), 'r');
        if ($handle === false) {
            return response()->json(['error' => 'Gagal membuka file CSV'], 400);
        }

        // Baris pertama dianggap header dan dilewati — tidak dicocokkan posisinya,
        // supaya urutan kolom di file tetap harus sama dengan template yang disediakan.
        fgetcsv($handle);

        $klienfk = Auth::user()->klienfk ?? 1;
        $seenCodes = [];
        $results = [];
        $rowNumber = 1;

        while (($line = fgetcsv($handle)) !== false) {
            $rowNumber++;

            if (count(array_filter($line, fn ($v) => trim((string) $v) !== '')) === 0) {
                continue; // baris kosong, skip tanpa dihitung error
            }

            $row = array_combine(self::IMPORT_HEADER, array_pad(array_slice($line, 0, count(self::IMPORT_HEADER)), count(self::IMPORT_HEADER), null));
            $row = array_map(fn ($v) => is_string($v) ? trim($v) : $v, $row);

            $error = $this->importRow($row, $klienfk, $seenCodes);

            $results[] = [
                'row' => $rowNumber,
                'code' => $row['code'],
                'status' => $error ? 'error' : 'success',
                'message' => $error,
            ];

            if (! $error) {
                $seenCodes[] = $row['code'];
            }
        }
        fclose($handle);

        $success = count(array_filter($results, fn ($r) => $r['status'] === 'success'));

        return response()->json([
            'message' => 'success',
            'summary' => [
                'total' => count($results),
                'success' => $success,
                'failed' => count($results) - $success,
            ],
            'details' => $results,
        ], 200);
    }

    /**
     * Validate + insert satu baris produk. Return null kalau sukses, atau pesan error.
     */
    private function importRow(array $row, $klienfk, array $seenCodes): ?string
    {
        if (empty($row['code'])) {
            return 'Kode produk wajib diisi';
        }
        if (empty($row['name'])) {
            return 'Nama produk wajib diisi';
        }
        if ($row['strength'] === null || $row['strength'] === '' || ! is_numeric($row['strength'])) {
            return 'Strength wajib diisi angka';
        }
        if (empty($row['unit'])) {
            return 'Unit wajib diisi';
        }

        if (in_array($row['code'], $seenCodes, true)) {
            return "Kode '{$row['code']}' duplikat di dalam file ini";
        }
        if (Product::where('code', $row['code'])->exists()) {
            return "Kode '{$row['code']}' sudah dipakai produk lain";
        }

        $unit = UnitProduct::where('name', $row['unit'])->first();
        if (! $unit) {
            return "Unit '{$row['unit']}' tidak ditemukan — buat dulu di master data Unit";
        }

        $satuanId = null;
        if (! empty($row['satuan'])) {
            $satuan = SatuanProduct::where('name', $row['satuan'])->first();
            if (! $satuan) {
                return "Satuan '{$row['satuan']}' tidak ditemukan — buat dulu di master data Satuan";
            }
            $satuanId = $satuan->id;
        }

        $kategoriId = null;
        if (! empty($row['kategori'])) {
            $kategori = ProductCategories::where('name', $row['kategori'])->first();
            if (! $kategori) {
                return "Kategori '{$row['kategori']}' tidak ditemukan — buat dulu di master data Kategori";
            }
            $kategoriId = $kategori->id;
        }

        $toBool = fn ($v) => in_array(strtolower(trim((string) $v)), ['1', 'ya', 'yes', 'true'], true);

        Product::create([
            'klienfk' => $klienfk,
            'code' => $row['code'],
            'name' => $row['name'],
            'strength' => $row['strength'],
            'unitfk' => $unit->id,
            'satuanfk' => $satuanId,
            'kategorifk' => $kategoriId,
            'is_fornas' => $toBool($row['is_fornas'] ?? null),
            'is_generik' => $toBool($row['is_generik'] ?? null),
            'is_antibiotik' => $toBool($row['is_antibiotik'] ?? null),
            'is_psikotropika' => $toBool($row['is_psikotropika'] ?? null),
            'is_narkotika' => $toBool($row['is_narkotika'] ?? null),
            'is_kronis' => $toBool($row['is_kronis'] ?? null),
            'harga_beli' => is_numeric($row['harga_beli'] ?? null) ? $row['harga_beli'] : 0,
            'hpp' => is_numeric($row['hpp'] ?? null) ? $row['hpp'] : 0,
            'harga_jual' => is_numeric($row['harga_jual'] ?? null) ? $row['harga_jual'] : 0,
        ]);

        return null;
    }
}
