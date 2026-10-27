<?php

namespace App\Http\Controllers\Website;

use App\Helpers\PosSaleHelper;
use App\Helpers\ProductQtyHelper;
use App\Http\Controllers\Controller;
use App\Models\Payments;
use App\Models\PosSale;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class PosController extends Controller
{
    public function index()
    {
        $payments = Payments::get();

        return view('pages/pos/index', compact('payments'));
    }

    public function getProductList(Request $request)
    {
        $q = $request->q;

        $products = Product::where('klienfk', Auth::user()->klienfk ?? 1)
            ->when($q, function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('code', 'like', "%{$q}%");
            })
            ->limit(20)
            ->get(['id', 'code', 'name', 'harga_jual']);

        $balances = app(ProductQtyHelper::class)
            ->getBalancesByProducts($products->pluck('id')->toArray());

        $data = $products->map(function ($product) use ($balances) {
            return [
                'id' => $product->id,
                'code' => $product->code,
                'name' => $product->name,
                'harga_jual' => (float) $product->harga_jual,
                'stock' => $balances[$product->id] ?? 0,
            ];
        });

        return response()->json(['message' => 'success', 'data' => $data], 200);
    }

    public function checkout(Request $request)
    {
        try {
            $sale = app(PosSaleHelper::class)->checkout($request->all());

            return response()->json([
                'message' => 'success',
                'data' => ['id' => $sale->id, 'no_sale' => $sale->no_sale],
            ], 200);
        } catch (Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function void($id)
    {
        try {
            app(PosSaleHelper::class)->void((int) $id);

            return response()->json(['message' => 'success'], 200);
        } catch (Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function getList(Request $request)
    {
        $search = $request->input('search.value');

        $query = PosSale::where('klienfk', Auth::user()->klienfk ?? 1);

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('no_sale', 'like', "%{$search}%")
                    ->orWhere('buyer_name', 'like', "%{$search}%");
            });
        }

        $recordsTotal = PosSale::where('klienfk', Auth::user()->klienfk ?? 1)->count();
        $recordsFiltered = $query->count();

        $data = $query->orderBy('id', 'desc')
            ->skip((int) $request->input('start', 0))
            ->take((int) $request->input('length', 10))
            ->get()
            ->map(function ($sale) {
                return [
                    'id' => $sale->id,
                    'no_sale' => $sale->no_sale,
                    'created_at' => $sale->created_at->format('Y-m-d H:i:s'),
                    'buyer_name' => $sale->buyer_name ?? '-',
                    'total_net' => (float) $sale->total_net,
                    'status' => $sale->status,
                ];
            });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    public function receipt($id)
    {
        $sale = PosSale::with(['items', 'payment', 'userhandler'])->findOrFail($id);
        $companyName = Setting::where('parameter', 'company_name')->first();
        $companyAddress = Setting::where('parameter', 'company_address')->first();
        $companyPhone = Setting::where('parameter', 'company_phone')->first();

        return $this->viewOrPdf(
            'print.pos.receipt',
            compact('sale', 'companyName', 'companyAddress', 'companyPhone'),
            "struk-{$sale->no_sale}.pdf"
        );
    }
}
