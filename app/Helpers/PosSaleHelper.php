<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Models\PosSale;
use App\Models\PosSaleItem;
use App\Models\Product;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

/**
 * Orchestrates POS (apotek) sales: multi-item checkout against ProductQtyHelper's
 * FEFO stock-out, a single ledger entry via TransactionHelper, and void/refund.
 */
final class PosSaleHelper
{
    private const DEFAULT_LOCATION_ID = 1;

    public function checkout(array $data): PosSale
    {
        $items = $data['items'] ?? [];
        if (empty($items) || ! is_array($items)) {
            throw new InvalidArgumentException('Keranjang tidak boleh kosong');
        }

        $fkPayment = $data['fk_payment'] ?? null;
        if (empty($fkPayment) || ! DB::table('m_payments')->where('id', $fkPayment)->exists()) {
            throw new InvalidArgumentException('Metode pembayaran tidak valid');
        }

        $locationId = (int) ($data['location_id'] ?? self::DEFAULT_LOCATION_ID);

        // Resolve harga_jual dari DB (bukan dari input client) supaya tidak bisa dimanipulasi,
        // diskon-nya saja yang boleh diisi kasir.
        $resolvedItems = [];
        $totalGross = 0.0;
        $totalDiscount = 0.0;

        foreach ($items as $item) {
            $productId = (int) ($item['product_id'] ?? 0);
            $qty = (float) ($item['qty'] ?? 0);

            if ($productId <= 0) {
                throw new InvalidArgumentException('product_id wajib diisi');
            }
            if ($qty <= 0) {
                throw new InvalidArgumentException('qty harus lebih dari 0');
            }

            $product = Product::find($productId);
            if (! $product) {
                throw new InvalidArgumentException("Produk #{$productId} tidak ditemukan");
            }

            $price = (float) $product->harga_jual;
            $lineGross = $qty * $price;

            $discount = (float) ($item['discount'] ?? 0);
            if ($discount < 0) {
                throw new InvalidArgumentException('Diskon tidak boleh negatif');
            }
            $discount = min($discount, $lineGross);

            $subtotal = $lineGross - $discount;

            $resolvedItems[] = [
                'product_id' => $productId,
                'product_name' => $product->name,
                'qty' => $qty,
                'price' => $price,
                'discount' => $discount,
                'subtotal' => $subtotal,
            ];

            $totalGross += $lineGross;
            $totalDiscount += $discount;
        }

        $totalNet = $totalGross - $totalDiscount;

        return DB::transaction(function () use ($resolvedItems, $totalGross, $totalDiscount, $totalNet, $fkPayment, $locationId, $data) {
            $sale = PosSale::create([
                'klienfk' => Auth::user()->klienfk ?? 1,
                'no_sale' => $this->generateSaleNumber(),
                'buyer_name' => $data['buyer_name'] ?? null,
                'buyer_phone' => $data['buyer_phone'] ?? null,
                'userhandler_id' => Auth::id(),
                'fk_payment' => $fkPayment,
                'total_gross' => $totalGross,
                'total_discount' => $totalDiscount,
                'total_net' => $totalNet,
                'status' => 'completed',
            ]);

            $qtyHelper = app(ProductQtyHelper::class);

            foreach ($resolvedItems as $item) {
                $qtyHelper->stockOutFefo([
                    'product_id' => $item['product_id'],
                    'location_id' => $locationId,
                    'qty' => $item['qty'],
                    'source' => 'transaction',
                ]);

                PosSaleItem::create([
                    'possale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'location_id' => $locationId,
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'discount' => $item['discount'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            // Kalau diskon membuat total jadi 0 (barang gratis), tidak ada uang yang
            // berpindah, jadi tidak perlu bikin baris ledger (TransactionHelper
            // menolak amount <= 0). Stok tetap berkurang seperti biasa.
            if ($totalNet > 0) {
                $transaction = app(TransactionHelper::class)->createTransaction([
                    'transaction_type' => 'in',
                    'typecode' => 'POS',
                    'amount' => $totalNet,
                    'notes' => "Penjualan POS #{$sale->no_sale}",
                    'userhandler_id' => Auth::id(),
                ]);

                $sale->update(['transaction_id' => $transaction->id]);
            }

            return $sale->load('items');
        }, 3);
    }

    public function void(int $posSaleId): PosSale
    {
        $sale = PosSale::with('items')->findOrFail($posSaleId);

        if ($sale->status === 'voided') {
            throw new RuntimeException('Penjualan ini sudah di-void sebelumnya');
        }

        return DB::transaction(function () use ($sale) {
            $qtyHelper = app(ProductQtyHelper::class);

            foreach ($sale->items as $item) {
                $qtyHelper->returnFromCustomerIn([
                    'product_id' => $item->product_id,
                    'location_id' => $item->location_id,
                    'qty' => $item->qty,
                ]);
            }

            $refundId = null;
            if ((float) $sale->total_net > 0) {
                $refund = app(TransactionHelper::class)->createTransaction([
                    'transaction_type' => 'out',
                    'typecode' => 'POS',
                    'amount' => $sale->total_net,
                    'notes' => "Void penjualan POS #{$sale->no_sale}",
                    'is_refund' => true,
                    'userhandler_id' => Auth::id(),
                ]);
                $refundId = $refund->id;
            }

            $sale->update([
                'status' => 'voided',
                'voided_at' => now(),
                'voided_by' => Auth::id(),
                'refund_transaction_id' => $refundId,
            ]);

            return $sale;
        }, 3);
    }

    private function generateSaleNumber(int $maxAttempts = 5): string
    {
        $now = CarbonImmutable::now();

        for ($i = 0; $i < $maxAttempts; $i++) {
            $rand = Str::upper(Str::random(6));
            $no = "POS{$now->format('Ymd')}{$rand}{$now->format('His')}";
            if (! PosSale::query()->where('no_sale', $no)->exists()) {
                return $no;
            }
        }

        return 'POS'.$now->format('Ymd').Str::upper(str_replace('-', '', (string) Str::uuid()));
    }
}
