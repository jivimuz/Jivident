<?php

namespace App\Http\Controllers\Website;

use App\Helpers\ProcurementHelper;
use App\Http\Controllers\Controller;
use App\Models\ProcurementRequest;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProcurementController extends Controller
{
    public function index()
    {
        return view('pages/procurement/index');
    }

    public function requests()
    {
        return response()->json(['data' => ProcurementRequest::with('items')->where('klienfk', Auth::user()->klienfk)->latest('id')->paginate(20)]);
    }

    public function storeRequest(Request $request)
    {
        $data = $request->validate([
            'request_no' => 'required|string|max:40',
            'requesting_unit' => 'required|string|max:100',
            'reason' => 'nullable|string|max:5000',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.product_id' => 'nullable|integer',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.notes' => 'nullable|string|max:1000',
        ]);
        $requestModel = (new ProcurementHelper)->createRequest($data, Auth::user()->klienfk, Auth::id());

        return response()->json(['message' => 'Permintaan pengadaan dibuat.', 'data' => $requestModel], 201);
    }

    public function transition(Request $request, int $id)
    {
        $data = $request->validate(['status' => ['required', Rule::in(['submitted', 'approved', 'rejected', 'ordered'])]]);
        $model = ProcurementRequest::where('klienfk', Auth::user()->klienfk)->findOrFail($id);
        $model = (new ProcurementHelper)->transitionRequest($model, $data['status'], Auth::id());

        return response()->json(['message' => 'Status permintaan diperbarui.', 'data' => $model]);
    }

    public function storePurchaseOrder(Request $request)
    {
        $data = $request->validate([
            'procurement_request_id' => 'nullable|integer',
            'supplier_id' => 'required|integer',
            'po_no' => 'required|string|max:40',
            'po_date' => 'required|date',
            'notes' => 'nullable|string|max:5000',
        ]);
        if (! empty($data['procurement_request_id']) && ! ProcurementRequest::where('id', $data['procurement_request_id'])->where('klienfk', Auth::user()->klienfk)->exists()) {
            return response()->json(['message' => 'Permintaan pengadaan tidak ditemukan.'], 404);
        }
        $po = (new ProcurementHelper)->createPurchaseOrder($data, Auth::user()->klienfk, Auth::id());

        return response()->json(['message' => 'Purchase order dibuat.', 'data' => $po], 201);
    }

    public function purchaseOrders()
    {
        return response()->json(['data' => PurchaseOrder::where('klienfk', Auth::user()->klienfk)->latest('id')->paginate(20)]);
    }
}
