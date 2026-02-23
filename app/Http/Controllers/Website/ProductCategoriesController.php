<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\ProductCategories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductCategoriesController extends Controller
{
    public function getList(Request $request)
    {
        // 1. Definisi Kolom untuk Sorting
        $columns = [
            0 => 'id',
            1 => 'name',
            2 => 'description',
            3 => 'id', // Action column
        ];

        // 2. Base Query
        $query = DB::table('m_productcategories');

        // 3. Hitung Total Data (Semua data sebelum filter)
        $totalData = $query->count();
        $totalFiltered = $totalData;

        // 4. Input Handling (FIX: Cast ke int & Default Value)
        // Ini mencegah error "SQL syntax... offset" jika parameter null
        $limit = (int) $request->input('length', 10);
        $start = (int) $request->input('start', 0);
        $search = $request->input('search.value');

        // 5. Logic Searching
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%"); // Tambahkan description agar ikut ter-search
            });

            // Hitung ulang total setelah filter search
            $totalFiltered = $query->count();
        }

        // 6. Logic Sorting
        $orderColumnIndex = $request->input('order.0.column');
        $orderDir = $request->input('order.0.dir'); // asc atau desc

        if (isset($columns[$orderColumnIndex])) {
            $query->orderBy($columns[$orderColumnIndex], $orderDir);
        } else {
            $query->orderBy('id', 'desc'); // Default sort
        }

        // 7. Logic Pagination (FIX: Gunakan limit > 0)
        // Jika limit -1 (show all), maka skip offset/limit
        if ($limit > 0) {
            $query->offset($start)->limit($limit);
        }

        // 8. Ambil Data
        $dataRaw = $query->get();

        // 9. Formatting Output
        $data = [];
        foreach ($dataRaw as $row) {
            $nestedData = [];

            // Pastikan key ini sama dengan konfigurasi 'columns' di JS DataTables
            $nestedData['id'] = $row->id;
            $nestedData['name'] = $row->name;
            $nestedData['description'] = $row->description;

            $data[] = $nestedData;
        }

        // 10. Return JSON
        return response()->json([
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        ]);
    }

    public function store(Request $r)
    {
        ProductCategories::create($r->all());
    }
    public function edit($id)
    {
        return ProductCategories::find($id);
    }
    public function update(Request $r, $id)
    {
        ProductCategories::find($id)->update($r->all());
    }
    public function destroy($id)
    {
        ProductCategories::find($id)->delete();
    }
}
