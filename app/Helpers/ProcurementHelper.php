<?php

namespace App\Helpers;

use App\Models\ProcurementRequest;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ProcurementHelper
{
    public function createRequest(array $data, int $klienfk, int $userId): ProcurementRequest
    {
        if (empty($data['items'])) {
            throw new InvalidArgumentException('Permintaan harus memiliki minimal satu item.');
        }

        return DB::transaction(function () use ($data, $klienfk, $userId) {
            $request = ProcurementRequest::create([
                'klienfk' => $klienfk,
                'request_no' => $data['request_no'],
                'requesting_unit' => $data['requesting_unit'],
                'reason' => $data['reason'] ?? null,
                'status' => 'draft',
                'requested_by' => $userId,
            ]);
            $request->items()->createMany($data['items']);

            return $request->load('items');
        });
    }

    public function transitionRequest(ProcurementRequest $request, string $status, int $userId): ProcurementRequest
    {
        $allowed = [
            'draft' => ['submitted'],
            'submitted' => ['approved', 'rejected'],
            'approved' => ['ordered'],
            'rejected' => [],
            'ordered' => [],
        ];
        if (! in_array($status, $allowed[$request->status] ?? [], true)) {
            throw new InvalidArgumentException("Permintaan tidak dapat berpindah dari {$request->status} ke {$status}.");
        }

        $request->status = $status;
        if ($status === 'approved') {
            $request->approved_by = $userId;
            $request->approved_at = now();
        }
        $request->save();

        return $request->refresh();
    }

    public function createPurchaseOrder(array $data, int $klienfk, int $userId): PurchaseOrder
    {
        return PurchaseOrder::create([
            'klienfk' => $klienfk,
            'procurement_request_id' => $data['procurement_request_id'] ?? null,
            'supplier_id' => $data['supplier_id'],
            'po_no' => $data['po_no'],
            'po_date' => $data['po_date'],
            'notes' => $data['notes'] ?? null,
            'created_by' => $userId,
        ]);
    }
}
