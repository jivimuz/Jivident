<?php

namespace Tests\Feature;

use App\Helpers\ProductQtyHelper;
use App\Models\Module;
use App\Models\Product;
use App\Models\ReceiveOrder;
use App\Models\ReceiveOrderDetail;
use App\Models\Serial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosControllerTest extends TestCase
{
    use RefreshDatabase;

    private function authorizedUser(): User
    {
        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addMonth()->toDateString(),
            'last_check' => now(),
        ]);

        $user = User::first();
        $user->access = (string) Module::where('code', 'POS')->value('id');
        $user->save();

        return $user;
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

        app(ProductQtyHelper::class)->processReceiveOrder($receiveOrder->id, 1);

        return $product;
    }

    public function test_pos_page_requires_access(): void
    {
        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addMonth()->toDateString(),
            'last_check' => now(),
        ]);
        $user = User::first();
        $user->access = '999999';
        $user->save();

        $response = $this->actingAs($user)->get('/pos');

        $response->assertRedirect(route('error'));
    }

    public function test_checkout_end_to_end_and_print_receipt(): void
    {
        $user = $this->authorizedUser();
        $product = $this->makeStockedProduct(20000, 10);

        $checkout = $this->actingAs($user)->post('/pos/checkout', [
            'fk_payment' => 1,
            'buyer_name' => 'Budi',
            'items' => [
                ['product_id' => $product->id, 'qty' => 2],
            ],
        ]);

        $checkout->assertOk();
        $saleId = $checkout->json('data.id');
        $noSale = $checkout->json('data.no_sale');
        $this->assertNotNull($saleId);

        $receiptHtml = $this->actingAs($user)->get("/pos/receipt/{$saleId}");
        $receiptHtml->assertOk();
        $receiptHtml->assertSee($noSale);

        $receiptPdf = $this->actingAs($user)->get("/pos/receipt/{$saleId}?pdf=1");
        $receiptPdf->assertOk();
        $this->assertSame('application/pdf', $receiptPdf->headers->get('Content-Type'));
    }

    public function test_checkout_rejects_insufficient_stock_via_http(): void
    {
        $user = $this->authorizedUser();
        $product = $this->makeStockedProduct(10000, 1);

        $response = $this->actingAs($user)->post('/pos/checkout', [
            'fk_payment' => 1,
            'items' => [
                ['product_id' => $product->id, 'qty' => 5],
            ],
        ]);

        $response->assertStatus(400);
        $response->assertJsonStructure(['error']);
    }

    public function test_void_via_http(): void
    {
        $user = $this->authorizedUser();
        $product = $this->makeStockedProduct(10000, 10);

        $checkout = $this->actingAs($user)->post('/pos/checkout', [
            'fk_payment' => 1,
            'items' => [['product_id' => $product->id, 'qty' => 3]],
        ]);
        $saleId = $checkout->json('data.id');

        $voidResponse = $this->actingAs($user)->post("/pos/void/{$saleId}");
        $voidResponse->assertOk();

        $this->assertSame(10.0, app(ProductQtyHelper::class)->getStockBalance($product->id, 1));
    }

    public function test_get_product_list_returns_stock_balance(): void
    {
        $user = $this->authorizedUser();
        $product = $this->makeStockedProduct(15000, 7);

        $response = $this->actingAs($user)->post('/pos/getProductList', ['q' => 'Obat']);

        $response->assertOk();
        $response->assertJsonFragment(['id' => $product->id, 'stock' => 7]);
    }
}
