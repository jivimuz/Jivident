<?php

namespace Tests\Unit;

use App\Helpers\ProductQtyHelper;
use App\Models\Product;
use App\Models\ReceiveOrder;
use App\Models\ReceiveOrderDetail;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use RuntimeException;
use Tests\TestCase;

class ProductQtyHelperTest extends TestCase
{
    use RefreshDatabase;

    private ProductQtyHelper $helper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->helper = new ProductQtyHelper;
    }

    private function makeProduct(array $overrides = []): Product
    {
        return Product::create(array_merge([
            'klienfk' => 1,
            'code' => 'P-'.uniqid(),
            'name' => 'Obat Test',
        ], $overrides));
    }

    private function makeReceiveOrder(array $overrides = []): ReceiveOrder
    {
        return ReceiveOrder::create(array_merge([
            'klienfk' => 1,
            'no_receipt' => 'RO-'.uniqid(),
            'supplier_id' => 1,
            'no_faktur' => 'FKT-'.uniqid(),
            'tgl_faktur' => now()->toDateString(),
        ], $overrides));
    }

    private function makeReceiveOrderDetail(int $receiveOrderId, int $productId, array $overrides = []): ReceiveOrderDetail
    {
        return ReceiveOrderDetail::create(array_merge([
            'klienfk' => 1,
            'receiveorder_id' => $receiveOrderId,
            'product_id' => $productId,
            'batch_no' => 'BATCH-'.uniqid(),
            'expired_date' => now()->addYear()->toDateString(),
            'qty' => 10,
            'subtotal' => 0,
        ], $overrides));
    }

    public function test_process_receive_order_inserts_in_mutation_and_marks_processed(): void
    {
        $product = $this->makeProduct();
        $receiveOrder = $this->makeReceiveOrder();
        $this->makeReceiveOrderDetail($receiveOrder->id, $product->id, ['qty' => 20]);

        $result = $this->helper->processReceiveOrder($receiveOrder->id, 1);

        $this->assertTrue($result['success']);
        $this->assertSame(1, $result['mutations_inserted']);
        $this->assertDatabaseHas('t_productqty', [
            'receipt_id' => $receiveOrder->id,
            'product_id' => $product->id,
            'location_id' => 1,
            'qty' => 20,
            'type' => 'in',
            'source' => 'receive',
        ]);
        $this->assertSame(20.0, $this->helper->getStockBalance($product->id, 1));

        $receiveOrder->refresh();
        $this->assertTrue((bool) $receiveOrder->is_processed);
    }

    public function test_process_receive_order_applies_global_ppn_setting_to_hpp(): void
    {
        Setting::where('parameter', 'ppn_persen')->update(['value' => 10]);

        $product = $this->makeProduct(['hpp' => 0, 'harga_jual' => 0]);
        $receiveOrder = $this->makeReceiveOrder();
        $this->makeReceiveOrderDetail($receiveOrder->id, $product->id, [
            'qty' => 5,
            'harga_beli' => 1000,
        ]);

        $this->helper->processReceiveOrder($receiveOrder->id, 1);

        $product->refresh();

        // hpp = harga_beli + (harga_beli * ppn% / 100) = 1000 + 100 = 1100
        $this->assertSame(1100.0, $product->hpp);
        // harga_jual minimal = hpp * 1.5 = 1650
        $this->assertSame(1650.0, $product->harga_jual);
    }

    public function test_process_receive_order_throws_when_already_processed(): void
    {
        $product = $this->makeProduct();
        $receiveOrder = $this->makeReceiveOrder(['is_processed' => true]);
        $this->makeReceiveOrderDetail($receiveOrder->id, $product->id);

        $this->expectException(InvalidArgumentException::class);
        $this->helper->processReceiveOrder($receiveOrder->id, 1);
    }

    public function test_process_receive_order_throws_when_no_details(): void
    {
        $receiveOrder = $this->makeReceiveOrder();

        $this->expectException(InvalidArgumentException::class);
        $this->helper->processReceiveOrder($receiveOrder->id, 1);
    }

    public function test_stock_out_fefo_consumes_earliest_expiring_lot_first(): void
    {
        $product = $this->makeProduct();

        // Lot A kedaluwarsa lebih dulu, masuk belakangan -> harus tetap diambil duluan (FEFO)
        $roA = $this->makeReceiveOrder();
        $this->makeReceiveOrderDetail($roA->id, $product->id, [
            'qty' => 5,
            'expired_date' => now()->addMonths(1)->toDateString(),
        ]);
        $this->helper->processReceiveOrder($roA->id, 1);

        $roB = $this->makeReceiveOrder();
        $this->makeReceiveOrderDetail($roB->id, $product->id, [
            'qty' => 10,
            'expired_date' => now()->addMonths(6)->toDateString(),
        ]);
        $this->helper->processReceiveOrder($roB->id, 1);

        $result = $this->helper->stockOutFefo([
            'product_id' => $product->id,
            'location_id' => 1,
            'qty' => 7,
        ]);

        $this->assertTrue($result['success']);
        $this->assertSame(2, $result['mutations_inserted']);

        // Harus habiskan lot A (5) dulu lalu ambil 2 dari lot B
        $this->assertEqualsCanonicalizing(
            [$roA->id, $roB->id],
            array_column($result['used_lots'], 'receipt_id')
        );

        $lotA = collect($result['used_lots'])->firstWhere('receipt_id', $roA->id);
        $lotB = collect($result['used_lots'])->firstWhere('receipt_id', $roB->id);
        $this->assertSame(5.0, $lotA['qty']);
        $this->assertSame(2.0, $lotB['qty']);

        $this->assertSame(8.0, $this->helper->getStockBalance($product->id, 1)); // 15 - 7
    }

    public function test_stock_out_fefo_throws_when_insufficient_stock(): void
    {
        $product = $this->makeProduct();
        $receiveOrder = $this->makeReceiveOrder();
        $this->makeReceiveOrderDetail($receiveOrder->id, $product->id, ['qty' => 3]);
        $this->helper->processReceiveOrder($receiveOrder->id, 1);

        $this->expectException(InvalidArgumentException::class);
        $this->helper->stockOutFefo([
            'product_id' => $product->id,
            'location_id' => 1,
            'qty' => 10,
        ]);
    }

    public function test_stock_out_fefo_excludes_expired_lots_by_default(): void
    {
        $product = $this->makeProduct();

        $receiveOrder = $this->makeReceiveOrder();
        $this->makeReceiveOrderDetail($receiveOrder->id, $product->id, [
            'qty' => 5,
            'expired_date' => now()->subDay()->toDateString(), // sudah lewat
        ]);
        $this->helper->processReceiveOrder($receiveOrder->id, 1);

        $this->expectException(InvalidArgumentException::class);
        $this->helper->stockOutFefo([
            'product_id' => $product->id,
            'location_id' => 1,
            'qty' => 1,
        ]);
    }

    public function test_mark_expired_stock_moves_expired_lots_out(): void
    {
        $product = $this->makeProduct();
        $receiveOrder = $this->makeReceiveOrder();
        $this->makeReceiveOrderDetail($receiveOrder->id, $product->id, [
            'qty' => 4,
            'expired_date' => now()->subDay()->toDateString(),
        ]);
        $this->helper->processReceiveOrder($receiveOrder->id, 1);

        $result = $this->helper->markExpiredStock();

        $this->assertSame(1, $result['expired_mutations_inserted']);
        $this->assertDatabaseHas('t_productqty', [
            'product_id' => $product->id,
            'type' => 'out',
            'source' => 'expired',
            'qty' => 4,
        ]);
        $this->assertSame(0.0, $this->helper->getStockBalance($product->id, 1));
    }

    public function test_rebuild_receive_order_mutations_fails_once_stock_used(): void
    {
        $product = $this->makeProduct();
        $receiveOrder = $this->makeReceiveOrder();
        $this->makeReceiveOrderDetail($receiveOrder->id, $product->id, ['qty' => 10]);
        $this->helper->processReceiveOrder($receiveOrder->id, 1);

        $this->helper->stockOutFefo([
            'product_id' => $product->id,
            'location_id' => 1,
            'qty' => 2,
        ]);

        $this->expectException(RuntimeException::class);
        $this->helper->rebuildReceiveOrderMutations($receiveOrder->id, 1);
    }
}
