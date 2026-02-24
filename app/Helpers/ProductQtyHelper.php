<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Models\ReceiveOrder;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

final class ProductQtyHelper
{
    /**
     * Karena schema kamu expired_date masih NOT NULL,
     * helper ini pakai placeholder untuk item non-expired.
     * Kalau nanti kolom expired_date dibuat nullable, ubah method normalizeExpiredDate().
     */
    private const NO_EXPIRED_PLACEHOLDER = '2099-12-31';

    /**
     * Process receive order -> insert mutasi stok IN ke t_productqty
     *
     * Assumsi detail table: t_receiveorderdetails
     * Kolom minimal: receiveorder_id, product_id, qty, expired_date
     * Opsional: location_id
     */



    /* DOCUMENTATION

    $helper = app(\App\Helpers\ProductQtyHelper::class);



    // - receive order
    $res = $helper->processReceiveOrder($receiveOrderId, 1);

    // - rebuildreceiveorder
    $res = $helper->rebuildReceiveOrderMutations($receiveOrderId, 1);

    // - Ambil stok keluar (penjualan) FEFO
    $res = $helper->stockOutFefo([
        'product_id' => 10,
        'location_id' => 1,
        'qty' => 5,
        'source' => 'transaction',
    ]);

    // - Ambil stok keluar (penjualan) FEFO
    $res = $helper->stockOpnameAdjustTotal(
    productId: 10,
    locationId: 1,
    qtyFisik: 23

    //  - Move stock antar lokasi
    $res = $helper->moveStockFefo([
        'product_id' => 10,
        'from_location_id' => 1,
        'to_location_id' => 2,
        'qty' => 3,
    ]);

    // - Auto buang expired
    $res = $helper->markExpiredStock();

);
    */




    public function processReceiveOrder(int $receiveOrderId, ?int $defaultLocationId = 1): array
    {
        return DB::transaction(function () use ($receiveOrderId, $defaultLocationId) {
            $header = ReceiveOrder::where('id', $receiveOrderId)
                ->whereNull('deleted_at')
                ->first();

            if (!$header) {
                throw new InvalidArgumentException('Receive order tidak ditemukan');
            }

            if (($header->is_processed == true) || $header->tgl_processed) {
                throw new InvalidArgumentException('Receive order sudah diproses');
            }

            $details = DB::table('t_receiveorderdetails')
                ->where('receiveorder_id', $receiveOrderId)
                ->get();

            if ($details->isEmpty()) {
                throw new InvalidArgumentException('Detail receive order kosong');
            }

            $rows = [];
            foreach ($details as $d) {
                $qty = $this->toPositiveQty($d->qty ?? 0, 'qty detail receive');

                $locationId = (int)($d->location_id ?? $defaultLocationId ?? 1);
                if ($locationId <= 0) {
                    throw new InvalidArgumentException("location_id tidak valid di detail receive order #{$receiveOrderId}");
                }

                $rows[] = [
                    'receipt_id'   => $receiveOrderId,
                    'receipt_detail_id'   => $d->id,
                    'batch_no'   => $d->batch_no,
                    'product_id'   => (int)$d->product_id,
                    'location_id'  => $locationId,
                    'qty'          => $qty,
                    'source'       => 'receive',
                    'type'         => 'in',
                    'expired_date' => $this->normalizeExpiredDate($d->expired_date ?? null),
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];
            }
            $this->updateProductPrices($d->product_id, $d->harga_beli ?? 0, $d->ppn_persen ?? 0);

            DB::table('t_productqty')->insert($rows);

            DB::table('t_receiveorders')
                ->where('id', $receiveOrderId)
                ->update([
                    'is_processed'  => true,
                    'tgl_processed' => now()->toDateString(), // karena schema kamu date
                    'updated_at'    => now(),
                ]);

            return [
                'success' => true,
                'message' => 'Receive order berhasil diproses',
                'receiveorder_id' => $receiveOrderId,
                'mutations_inserted' => count($rows),
            ];
        });
    }

    /**
     * Rebuild mutasi receive order (hapus mutasi receive lama lalu insert ulang)
     * Aman hanya jika stok dari receipt ini belum pernah dipakai (tidak ada OUT dari receipt_id tsb).
     */
    public function rebuildReceiveOrderMutations(int $receiveOrderId, ?int $defaultLocationId = 1): array
    {
        return DB::transaction(function () use ($receiveOrderId, $defaultLocationId) {
            $header = DB::table('t_receiveorders')
                ->where('id', $receiveOrderId)
                ->whereNull('deleted_at')
                ->first();

            if (!$header) {
                throw new InvalidArgumentException('Receive order tidak ditemukan');
            }

            // lock semua row stok receipt ini
            DB::table('t_productqty')
                ->whereNull('deleted_at')
                ->where('receipt_id', $receiveOrderId)
                ->lockForUpdate()
                ->get(['id']);

            // Kalau sudah ada OUT dari receipt ini => jangan rebuild
            $outCount = DB::table('t_productqty')
                ->whereNull('deleted_at')
                ->where('receipt_id', $receiveOrderId)
                ->where('type', 'out')
                ->count();

            if ($outCount > 0) {
                throw new RuntimeException(
                    "Tidak bisa rebuild receive order karena stok dari receipt ini sudah pernah dipakai (OUT={$outCount})."
                );
            }

            // soft delete mutasi receive lama
            DB::table('t_productqty')
                ->whereNull('deleted_at')
                ->where('receipt_id', $receiveOrderId)
                ->where('source', 'receive')
                ->where('type', 'in')
                ->update([
                    'deleted_at' => now(),
                    'updated_at' => now(),
                ]);

            // set unprocessed dulu supaya method process bisa dipakai
            DB::table('t_receiveorders')
                ->where('id', $receiveOrderId)
                ->update([
                    'is_processed'  => false,
                    'tgl_processed' => null,
                    'updated_at'    => now(),
                ]);

            return $this->processReceiveOrder($receiveOrderId, $defaultLocationId);
        });
    }

    /**
     * Stock in manual (return, opname+, moving in, adjustment in, dll)
     */
    public function stockIn(array $data): int
    {
        $productId = $this->toPositiveInt($data['product_id'] ?? 0, 'product_id');
        $locationId = $this->toPositiveInt($data['location_id'] ?? 0, 'location_id');
        $qty = $this->toPositiveQty($data['qty'] ?? 0, 'qty');

        $source = (string)($data['source'] ?? 'transaction');
        $this->assertValidSource($source);

        $receiptId = isset($data['receipt_id']) && $data['receipt_id'] !== ''
            ? (int)$data['receipt_id']
            : null;

        return (int) DB::table('t_productqty')->insertGetId([
            'receipt_id'   => $receiptId,
            'product_id'   => $productId,
            'location_id'  => $locationId,
            'qty'          => $qty,
            'source'       => $source,
            'type'         => 'in',
            'expired_date' => $this->normalizeExpiredDate($data['expired_date'] ?? null),
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }

    /**
     * Stock out FEFO + FIFO
     * Lot diidentifikasi oleh: receipt_id + expired_date (sesuai schema kamu saat ini)
     *
     * Params:
     * - product_id (required)
     * - location_id (required)
     * - qty (required > 0)
     * - source (default transaction)
     * - allow_expired (default false)
     */
    public function stockOutFefo(array $data): array
    {
        $productId = $this->toPositiveInt($data['product_id'] ?? 0, 'product_id');
        $locationId = $this->toPositiveInt($data['location_id'] ?? 0, 'location_id');
        $qtyNeed = $this->toPositiveQty($data['qty'] ?? 0, 'qty');
        $source = (string)($data['source'] ?? 'transaction');
        $allowExpired = (bool)($data['allow_expired'] ?? false);

        $this->assertValidSource($source);

        return DB::transaction(function () use ($productId, $locationId, $qtyNeed, $source, $allowExpired) {
            // Lock row mutasi untuk produk-lokasi ini (biar race condition minim)
            DB::table('t_productqty')
                ->whereNull('deleted_at')
                ->where('product_id', $productId)
                ->where('location_id', $locationId)
                ->lockForUpdate()
                ->get(['id']);

            $lots = $this->getAvailableLotsQuery($productId, $locationId, !$allowExpired)
                ->orderByRaw("CASE WHEN expired_date IS NULL THEN 1 ELSE 0 END ASC")
                ->orderBy('expired_date', 'asc') // FEFO
                ->orderBy('first_in_at', 'asc')  // FIFO
                ->get();

            $remaining = $qtyNeed;
            $rows = [];

            foreach ($lots as $lot) {
                if ($remaining <= 0) {
                    break;
                }

                $available = (float)$lot->available_qty;
                if ($available <= 0) {
                    continue;
                }

                $take = min($available, $remaining);

                $rows[] = [
                    'receipt_id'   => $lot->receipt_id, // supaya jejak lot asal tetap kebaca
                    'product_id'   => $productId,
                    'location_id'  => $locationId,
                    'qty'          => $take,
                    'source'       => $source,
                    'type'         => 'out',
                    'expired_date' => $this->normalizeExpiredDate($lot->expired_date ?? null),
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];

                $remaining -= $take;
            }

            if ($remaining > 0) {
                throw new InvalidArgumentException("Stok tidak cukup. Kurang {$remaining}");
            }

            if (!empty($rows)) {
                DB::table('t_productqty')->insert($rows);
            }

            return [
                'success' => true,
                'requested_qty' => $qtyNeed,
                'mutations_inserted' => count($rows),
                'used_lots' => $rows,
            ];
        });
    }

    /**
     * Alias FIFO murni (abaikan expired urutan utama)
     * Tapi tetap bisa exclude expired by default
     */
    public function stockOutFifo(array $data): array
    {
        $productId = $this->toPositiveInt($data['product_id'] ?? 0, 'product_id');
        $locationId = $this->toPositiveInt($data['location_id'] ?? 0, 'location_id');
        $qtyNeed = $this->toPositiveQty($data['qty'] ?? 0, 'qty');
        $source = (string)($data['source'] ?? 'transaction');
        $allowExpired = (bool)($data['allow_expired'] ?? false);

        $this->assertValidSource($source);

        return DB::transaction(function () use ($productId, $locationId, $qtyNeed, $source, $allowExpired) {
            DB::table('t_productqty')
                ->whereNull('deleted_at')
                ->where('product_id', $productId)
                ->where('location_id', $locationId)
                ->lockForUpdate()
                ->get(['id']);

            $lots = $this->getAvailableLotsQuery($productId, $locationId, !$allowExpired)
                ->orderBy('first_in_at', 'asc')  // FIFO utama
                ->orderBy('expired_date', 'asc') // tie breaker
                ->get();

            $remaining = $qtyNeed;
            $rows = [];

            foreach ($lots as $lot) {
                if ($remaining <= 0) break;

                $available = (float)$lot->available_qty;
                if ($available <= 0) continue;

                $take = min($available, $remaining);

                $rows[] = [
                    'receipt_id'   => $lot->receipt_id,
                    'product_id'   => $productId,
                    'location_id'  => $locationId,
                    'qty'          => $take,
                    'source'       => $source,
                    'type'         => 'out',
                    'expired_date' => $this->normalizeExpiredDate($lot->expired_date ?? null),
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];

                $remaining -= $take;
            }

            if ($remaining > 0) {
                throw new InvalidArgumentException("Stok tidak cukup. Kurang {$remaining}");
            }

            DB::table('t_productqty')->insert($rows);

            return [
                'success' => true,
                'requested_qty' => $qtyNeed,
                'mutations_inserted' => count($rows),
                'used_lots' => $rows,
            ];
        });
    }

    /**
     * Move stock antar lokasi (OUT dari lokasi asal + IN ke lokasi tujuan)
     * FEFO dari lokasi asal
     */
    public function moveStockFefo(array $data): array
    {
        $productId = $this->toPositiveInt($data['product_id'] ?? 0, 'product_id');
        $fromLocationId = $this->toPositiveInt($data['from_location_id'] ?? 0, 'from_location_id');
        $toLocationId = $this->toPositiveInt($data['to_location_id'] ?? 0, 'to_location_id');
        $qty = $this->toPositiveQty($data['qty'] ?? 0, 'qty');

        if ($fromLocationId === $toLocationId) {
            throw new InvalidArgumentException('Lokasi asal dan tujuan tidak boleh sama');
        }

        return DB::transaction(function () use ($productId, $fromLocationId, $toLocationId, $qty, $data) {
            // OUT dari lokasi asal (moving)
            $out = $this->stockOutFefo([
                'product_id'    => $productId,
                'location_id'   => $fromLocationId,
                'qty'           => $qty,
                'source'        => 'moving',
                'allow_expired' => (bool)($data['allow_expired'] ?? false),
            ]);

            // IN ke lokasi tujuan preserve lot info (receipt_id + expired_date)
            $inRows = [];
            foreach ($out['used_lots'] as $lot) {
                $inRows[] = [
                    'receipt_id'   => $lot['receipt_id'] ?? null,
                    'product_id'   => $productId,
                    'location_id'  => $toLocationId,
                    'qty'          => $lot['qty'],
                    'source'       => 'moving',
                    'type'         => 'in',
                    'expired_date' => $this->normalizeExpiredDate($lot['expired_date'] ?? null),
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];
            }

            if (!empty($inRows)) {
                DB::table('t_productqty')->insert($inRows);
            }

            return [
                'success' => true,
                'product_id' => $productId,
                'from_location_id' => $fromLocationId,
                'to_location_id' => $toLocationId,
                'qty' => $qty,
                'out_lots' => $out['used_lots'],
                'in_rows_inserted' => count($inRows),
            ];
        });
    }

    /**
     * Retur ke supplier (stok keluar)
     */
    public function returnToSupplierFefo(array $data): array
    {
        $data['source'] = 'return';
        return $this->stockOutFefo($data);
    }

    /**
     * Retur dari customer (stok masuk)
     */
    public function returnFromCustomerIn(array $data): int
    {
        $data['source'] = 'return';
        return $this->stockIn($data);
    }

    /**
     * Koreksi stock opname total (tanpa lot)
     * Cocok buat awal implementasi.
     *
     * Kalau qty fisik > sistem => insert IN source=opname
     * Kalau qty fisik < sistem => insert OUT source=opname
     */
    public function stockOpnameAdjustTotal(int $productId, int $locationId, float $qtyFisik, ?string $expiredDate = null): array
    {
        if ($qtyFisik < 0) {
            throw new InvalidArgumentException('qty fisik tidak boleh negatif');
        }

        return DB::transaction(function () use ($productId, $locationId, $qtyFisik, $expiredDate) {
            DB::table('t_productqty')
                ->whereNull('deleted_at')
                ->where('product_id', $productId)
                ->where('location_id', $locationId)
                ->lockForUpdate()
                ->get(['id']);

            $qtySistem = $this->getStockBalance($productId, $locationId, false);
            $delta = round($qtyFisik - $qtySistem, 6);

            if (abs($delta) < 0.000001) {
                return [
                    'success' => true,
                    'message' => 'Tidak ada selisih opname',
                    'qty_system' => $qtySistem,
                    'qty_physical' => $qtyFisik,
                    'delta' => 0,
                    'mutation_id' => null,
                ];
            }

            $id = (int) DB::table('t_productqty')->insertGetId([
                'receipt_id'   => null,
                'product_id'   => $productId,
                'location_id'  => $locationId,
                'qty'          => abs($delta),
                'source'       => 'opname',
                'type'         => $delta > 0 ? 'in' : 'out',
                'expired_date' => $this->normalizeExpiredDate($expiredDate),
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            return [
                'success' => true,
                'message' => 'Opname adjustment berhasil',
                'qty_system' => $qtySistem,
                'qty_physical' => $qtyFisik,
                'delta' => $delta,
                'mutation_id' => $id,
            ];
        });
    }

    /**
     * Opname per lot (lot = receipt_id + expired_date)
     * Format $physicalLots:
     * [
     *   ['receipt_id' => 123, 'expired_date' => '2026-12-01', 'qty_fisik' => 10],
     *   ['receipt_id' => null, 'expired_date' => null, 'qty_fisik' => 2], // kalau item generic
     * ]
     */
    public function stockOpnameAdjustLots(int $productId, int $locationId, array $physicalLots): array
    {
        return DB::transaction(function () use ($productId, $locationId, $physicalLots) {
            DB::table('t_productqty')
                ->whereNull('deleted_at')
                ->where('product_id', $productId)
                ->where('location_id', $locationId)
                ->lockForUpdate()
                ->get(['id']);

            $systemLots = $this->getAvailableLots($productId, $locationId, includeOnlyNonExpired: false)
                ->keyBy(function ($r) {
                    return $this->lotKey($r->receipt_id ?? null, $r->expired_date ?? null);
                });

            $adjustments = [];

            foreach ($physicalLots as $lot) {
                $receiptId = isset($lot['receipt_id']) && $lot['receipt_id'] !== '' ? (int)$lot['receipt_id'] : null;
                $expired = $this->normalizeExpiredDate($lot['expired_date'] ?? null);
                $qtyFisik = (float)($lot['qty_fisik'] ?? 0);

                if ($qtyFisik < 0) {
                    throw new InvalidArgumentException('qty_fisik tidak boleh negatif');
                }

                $key = $this->lotKey($receiptId, $expired);
                $systemQty = (float)optional($systemLots->get($key))->available_qty;
                $delta = round($qtyFisik - $systemQty, 6);

                if (abs($delta) < 0.000001) {
                    continue;
                }

                $row = [
                    'receipt_id'   => $receiptId,
                    'product_id'   => $productId,
                    'location_id'  => $locationId,
                    'qty'          => abs($delta),
                    'source'       => 'opname',
                    'type'         => $delta > 0 ? 'in' : 'out',
                    'expired_date' => $expired,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];

                DB::table('t_productqty')->insert($row);
                $adjustments[] = $row;
            }

            return [
                'success' => true,
                'adjustments_count' => count($adjustments),
                'adjustments' => $adjustments,
            ];
        });
    }

    /**
     * Keluarkan stok yang expired (source=expired, type=out)
     * cutoff default hari ini (strict: expired < today)
     */
    public function markExpiredStock(?int $productId = null, ?int $locationId = null, ?string $cutoffDate = null): array
    {
        $cutoff = $cutoffDate ? Carbon::parse($cutoffDate)->toDateString() : now()->toDateString();

        return DB::transaction(function () use ($productId, $locationId, $cutoff) {
            $q = DB::table('t_productqty')
                ->whereNull('deleted_at')
                ->whereNotNull('expired_date')
                ->whereDate('expired_date', '<', $cutoff);

            if ($productId) {
                $q->where('product_id', $productId);
            }
            if ($locationId) {
                $q->where('location_id', $locationId);
            }

            // lock rows
            $q->clone()
                ->lockForUpdate()
                ->get(['id']);

            $lots = $q->selectRaw("
                    receipt_id,
                    product_id,
                    location_id,
                    expired_date,
                    MIN(created_at) as first_in_at,
                    COALESCE(SUM(CASE
                        WHEN type='in' THEN qty
                        WHEN type='out' THEN -qty
                        ELSE 0 END), 0) as available_qty
                ")
                ->groupBy('receipt_id', 'product_id', 'location_id', 'expired_date')
                ->havingRaw("COALESCE(SUM(CASE WHEN type='in' THEN qty WHEN type='out' THEN -qty ELSE 0 END), 0) > 0")
                ->get();

            if ($lots->isEmpty()) {
                return [
                    'success' => true,
                    'expired_mutations_inserted' => 0,
                    'rows' => [],
                ];
            }

            $rows = [];
            foreach ($lots as $lot) {
                $qty = (float)$lot->available_qty;
                if ($qty <= 0) continue;

                $rows[] = [
                    'receipt_id'   => $lot->receipt_id,
                    'product_id'   => (int)$lot->product_id,
                    'location_id'  => (int)$lot->location_id,
                    'qty'          => $qty,
                    'source'       => 'expired',
                    'type'         => 'out',
                    'expired_date' => $this->normalizeExpiredDate($lot->expired_date),
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];
            }

            if (!empty($rows)) {
                DB::table('t_productqty')->insert($rows);
            }

            return [
                'success' => true,
                'expired_mutations_inserted' => count($rows),
                'rows' => $rows,
            ];
        });
    }

    /**
     * Saldo stok per product+location (signed sum)
     */
    public function getStockBalance(int $productId, int $locationId, bool $excludeExpired = false): float
    {
        $q = DB::table('t_productqty')
            ->whereNull('deleted_at')
            ->where('product_id', $productId)
            ->where('location_id', $locationId);

        if ($excludeExpired) {
            $today = now()->toDateString();
            $q->where(function ($w) use ($today) {
                $w->whereNull('expired_date')
                    ->orWhereDate('expired_date', '>=', $today);
            });
        }

        return (float) $q->selectRaw("
            COALESCE(SUM(CASE
                WHEN type='in' THEN qty
                WHEN type='out' THEN -qty
                ELSE 0
            END), 0) as saldo
        ")->value('saldo');
    }

    /**
     * Ambil saldo stok per banyak product (buat list/datatable)
     *
     * Return format:
     * [
     *   product_id => qty,
     *   ...
     * ]
     */
    public function getBalancesByProducts(array $productIds, ?int $locationId = null, bool $excludeExpired = false): array
    {
        $productIds = array_values(array_filter(array_map('intval', $productIds), fn($v) => $v > 0));
        if (empty($productIds)) {
            return [];
        }

        $q = DB::table('t_productqty')
            ->whereNull('deleted_at')
            ->whereIn('product_id', $productIds);

        if ($locationId !== null) {
            $q->where('location_id', (int)$locationId);
        }

        if ($excludeExpired) {
            $today = now()->toDateString();
            $q->where(function ($w) use ($today) {
                $w->whereNull('expired_date')
                    ->orWhereDate('expired_date', '>=', $today);
            });
        }

        $rows = $q->selectRaw("
                product_id,
                COALESCE(SUM(CASE
                    WHEN type='in' THEN qty
                    WHEN type='out' THEN -qty
                    ELSE 0 END),0) as qty
            ")
            ->groupBy('product_id')
            ->get();

        $result = [];
        foreach ($rows as $r) {
            $result[(int)$r->product_id] = (float)$r->qty;
        }

        // Isi default 0 untuk yang gak ada row
        foreach ($productIds as $pid) {
            if (!array_key_exists($pid, $result)) {
                $result[$pid] = 0.0;
            }
        }

        return $result;
    }

    /**
     * Lot tersedia (saldo > 0), grouped by receipt_id + expired_date + product/location
     */
    public function getAvailableLots(int $productId, int $locationId, bool $includeOnlyNonExpired = true): Collection
    {
        return $this->getAvailableLotsQuery($productId, $locationId, $includeOnlyNonExpired)
            ->orderByRaw("CASE WHEN expired_date IS NULL THEN 1 ELSE 0 END ASC")
            ->orderBy('expired_date', 'asc')
            ->orderBy('first_in_at', 'asc')
            ->get();
    }

    /**
     * Debug helper: cek apakah stok cukup sebelum save transaksi
     */
    public function hasEnoughStock(int $productId, int $locationId, float $qty, bool $excludeExpired = true): bool
    {
        $saldo = $this->getStockBalance($productId, $locationId, $excludeExpired);
        return $saldo >= $qty;
    }

    /**
     * =========================
     * INTERNAL METHODS
     * =========================
     */

    private function getAvailableLotsQuery(int $productId, int $locationId, bool $includeOnlyNonExpired = true)
    {
        $q = DB::table('t_productqty')
            ->whereNull('deleted_at')
            ->where('product_id', $productId)
            ->where('location_id', $locationId);

        if ($includeOnlyNonExpired) {
            $today = now()->toDateString();
            $q->where(function ($w) use ($today) {
                // placeholder non-expired juga lolos karena tanggal jauh
                $w->whereNull('expired_date')
                    ->orWhereDate('expired_date', '>=', $today);
            });
        }

        return $q->selectRaw("
                receipt_id,
                product_id,
                location_id,
                expired_date,
                MIN(created_at) as first_in_at,
                COALESCE(SUM(CASE
                    WHEN type='in' THEN qty
                    WHEN type='out' THEN -qty
                    ELSE 0 END),0) as available_qty
            ")
            ->groupBy('receipt_id', 'product_id', 'location_id', 'expired_date')
            ->havingRaw("
                COALESCE(SUM(CASE
                    WHEN type='in' THEN qty
                    WHEN type='out' THEN -qty
                    ELSE 0 END),0) > 0
            ");
    }

    private function normalizeExpiredDate($date): string
    {
        if ($date === null || $date === '' || $date === '0000-00-00') {
            // Karena schema sekarang not null.
            return self::NO_EXPIRED_PLACEHOLDER;
        }

        return Carbon::parse((string)$date)->toDateString();
    }

    private function toPositiveInt($value, string $field): int
    {
        $int = (int)$value;
        if ($int <= 0) {
            throw new InvalidArgumentException("{$field} wajib integer > 0");
        }
        return $int;
    }

    private function toPositiveQty($value, string $field): float
    {
        if ($value === null || $value === '' || !is_numeric($value)) {
            throw new InvalidArgumentException("{$field} wajib numeric");
        }

        $qty = (float)$value;
        if ($qty <= 0) {
            throw new InvalidArgumentException("{$field} harus > 0");
        }

        return $qty;
    }

    private function assertValidSource(string $source): void
    {
        $allowed = ['receive', 'transaction', 'return', 'opname', 'expired', 'moving'];

        if (!in_array($source, $allowed, true)) {
            throw new InvalidArgumentException("source tidak valid. Allowed: " . implode(', ', $allowed));
        }
    }

    private function lotKey($receiptId, $expiredDate): string
    {
        return (string)($receiptId ?? '') . '|' . (string)($expiredDate ?? '');
    }

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
}
