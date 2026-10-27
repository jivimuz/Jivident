<?php

namespace Tests\Unit;

use App\Helpers\PosSaleHelper;
use App\Helpers\ProductQtyHelper;
use App\Models\Product;
use App\Models\ReceiveOrder;
use App\Models\ReceiveOrderDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use RuntimeException;
use Tests\TestCase;

class PosSaleHelperTest extends TestCase
{
    use RefreshDatabase;

    private PosSaleHelper $helper;

    private ProductQtyHelper $qtyHelper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->helper = app(PosSaleHelper::class);
        $this->qtyHelper = app(ProductQtyHelper::class);
        $this->actingAs(User::first());
    }

    private function makeStockedProduct(float $hargaJual, float $qty): Product
    {
        $product = Product::create([
            'klienfk' => 1,
            'code' => 'P-'.uniqid(),
            'name' => 'Obat Test',
            'harga_jual' => $hargaJual,
        ]);

        $receiveOrder = ReceiveOrder::create([
            'klienfk' => 1,
            'no_receipt' => 'RO-'.uniqid(),
            'supplier_id' => 1,
            'no_faktur' => 'FKT-'.uniqid(),
            'tgl_faktur' => now()->toDateString(),
        ]);

        ReceiveOrderDetail::create([
            'klienfk' => 1,
            'receiveorder_id' => $receiveOrder->id,
            'product_id' => $product->id,
            'batch_no' => 'BATCH-'.uniqid(),
            'expired_date' => now()->addYear()->toDateString(),
            'qty' => $qty,
            'subtotal' => 0,
        ]);

        $this->qtyHelper->processReceiveOrder($receiveOrder->id, 1);

        return $product;
    }

    public function test_checkout_creates_sale_deducts_stock_and_records_ledger_transaction(): void
    {
        $product = $this->makeStockedProduct(hargaJual: 15000, qty: 10);

        $sale = $this->helper->checkout([
            'fk_payment' => 1,
            'buyer_name' => 'Budi',
            'buyer_phone' => '0812000000',
            'items' => [
                ['product_id' => $product->id, 'qty' => 3],
            ],
        ]);

        $this->assertSame('completed', $sale->status);
        $this->assertSame(45000.0, (float) $sale->total_net);
        $this->assertNotNull($sale->transaction_id);
        $this->assertSame(7.0, $this->qtyHelper->getStockBalance($product->id, 1));
        $this->assertDatabaseHas('t_transactions', [
            'id' => $sale->transaction_id,
            'transaction_type' => 'in',
            'typecode' => 'POS',
        ]);
    }

    public function test_checkout_applies_per_item_discount(): void
    {
        $product = $this->makeStockedProduct(hargaJual: 10000, qty: 5);

        $sale = $this->helper->checkout([
            'fk_payment' => 1,
            'items' => [
                ['product_id' => $product->id, 'qty' => 2, 'discount' => 3000],
            ],
        ]);

        // gross 20000, discount 3000 -> net 17000
        $this->assertSame(20000.0, (float) $sale->total_gross);
        $this->assertSame(3000.0, (float) $sale->total_discount);
        $this->assertSame(17000.0, (float) $sale->total_net);
    }

    public function test_discount_cannot_exceed_line_total(): void
    {
        $product = $this->makeStockedProduct(hargaJual: 10000, qty: 5);

        $sale = $this->helper->checkout([
            'fk_payment' => 1,
            'items' => [
                ['product_id' => $product->id, 'qty' => 1, 'discount' => 999999],
            ],
        ]);

        $this->assertSame(0.0, (float) $sale->total_net);
        $this->assertNull($sale->transaction_id);

        // Void sale gratis (total_net=0) tidak boleh error walau tidak ada transaksi untuk dibalik.
        $voided = $this->helper->void($sale->id);
        $this->assertSame('voided', $voided->status);
        $this->assertNull($voided->refund_transaction_id);
    }

    public function test_checkout_throws_when_stock_insufficient(): void
    {
        $product = $this->makeStockedProduct(hargaJual: 10000, qty: 2);

        $this->expectException(InvalidArgumentException::class);

        $this->helper->checkout([
            'fk_payment' => 1,
            'items' => [
                ['product_id' => $product->id, 'qty' => 5],
            ],
        ]);
    }

    public function test_checkout_rejects_empty_cart(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->helper->checkout(['fk_payment' => 1, 'items' => []]);
    }

    public function test_checkout_rejects_invalid_payment_method(): void
    {
        $product = $this->makeStockedProduct(hargaJual: 10000, qty: 5);

        $this->expectException(InvalidArgumentException::class);

        $this->helper->checkout([
            'fk_payment' => 999999,
            'items' => [['product_id' => $product->id, 'qty' => 1]],
        ]);
    }

    public function test_void_restores_stock_and_reverses_ledger(): void
    {
        $product = $this->makeStockedProduct(hargaJual: 10000, qty: 10);

        $sale = $this->helper->checkout([
            'fk_payment' => 1,
            'items' => [['product_id' => $product->id, 'qty' => 4]],
        ]);

        $this->assertSame(6.0, $this->qtyHelper->getStockBalance($product->id, 1));

        $voided = $this->helper->void($sale->id);

        $this->assertSame('voided', $voided->status);
        $this->assertNotNull($voided->refund_transaction_id);
        $this->assertSame(10.0, $this->qtyHelper->getStockBalance($product->id, 1));
        $this->assertDatabaseHas('t_transactions', [
            'id' => $voided->refund_transaction_id,
            'transaction_type' => 'out',
            'is_refund' => 1,
        ]);
    }

    public function test_cannot_void_an_already_voided_sale(): void
    {
        $product = $this->makeStockedProduct(hargaJual: 10000, qty: 10);

        $sale = $this->helper->checkout([
            'fk_payment' => 1,
            'items' => [['product_id' => $product->id, 'qty' => 2]],
        ]);

        $this->helper->void($sale->id);

        $this->expectException(RuntimeException::class);
        $this->helper->void($sale->id);
    }
}
