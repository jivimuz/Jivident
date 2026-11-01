<?php

namespace Tests\Feature;

use App\Models\Module;
use App\Models\Product;
use App\Models\ProductQTY;
use App\Models\Serial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockOpnameControllerTest extends TestCase
{
    use RefreshDatabase;

    private function authorizedUser(int $klienfk = 1): User
    {
        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addMonth()->toDateString(),
            'last_check' => now(),
        ]);

        $user = User::first();
        $user->klienfk = $klienfk;
        $user->access = (string) Module::where('code', 'SO')->value('id');
        $user->save();

        return $user;
    }

    private function makeProduct(int $klienfk = 1, array $overrides = []): Product
    {
        return Product::create(array_merge([
            'klienfk' => $klienfk,
            'code' => 'P-'.uniqid(),
            'name' => 'Obat Opname Test',
        ], $overrides));
    }

    public function test_page_requires_access(): void
    {
        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addMonth()->toDateString(),
            'last_check' => now(),
        ]);
        $user = User::first();
        $user->access = '999999';
        $user->save();

        $response = $this->actingAs($user)->get('/stock-opname');

        $response->assertRedirect(route('error'));
    }

    public function test_page_renders_for_an_authorized_user(): void
    {
        $user = $this->authorizedUser();

        $response = $this->actingAs($user)->get('/stock-opname');

        $response->assertOk();
    }

    public function test_get_list_reports_current_system_stock(): void
    {
        $user = $this->authorizedUser();
        $product = $this->makeProduct();

        ProductQTY::create([
            'product_id' => $product->id,
            'location_id' => 1,
            'qty' => 15,
            'source' => 'receive',
            'type' => 'in',
            'expired_date' => now()->addYear()->toDateString(),
        ]);

        $response = $this->actingAs($user)->post('/stock-opname/getList');

        $response->assertOk();
        $row = collect($response->json('data'))->firstWhere('id', $product->id);
        $this->assertNotNull($row);
        $this->assertEquals(15, $row['qty_sistem']);
    }

    public function test_get_list_does_not_include_products_from_another_branch(): void
    {
        $user = $this->authorizedUser(klienfk: 1);
        $this->makeProduct(klienfk: 2, overrides: ['name' => 'Obat Cabang Lain']);

        $response = $this->actingAs($user)->post('/stock-opname/getList');

        $response->assertOk();
        $names = collect($response->json('data'))->pluck('name');
        $this->assertFalse($names->contains('Obat Cabang Lain'));
    }

    public function test_adjust_inserts_in_mutation_when_physical_qty_is_higher(): void
    {
        $user = $this->authorizedUser();
        $product = $this->makeProduct();

        $response = $this->actingAs($user)->post('/stock-opname/adjust', [
            'product_id' => $product->id,
            'qty_fisik' => 10,
        ]);

        $response->assertOk();
        $response->assertJson(['delta' => 10]);
        $this->assertDatabaseHas('t_productqty', [
            'product_id' => $product->id,
            'type' => 'in',
            'source' => 'opname',
            'qty' => 10,
        ]);
    }

    public function test_adjust_inserts_out_mutation_when_physical_qty_is_lower(): void
    {
        $user = $this->authorizedUser();
        $product = $this->makeProduct();

        ProductQTY::create([
            'product_id' => $product->id,
            'location_id' => 1,
            'qty' => 20,
            'source' => 'receive',
            'type' => 'in',
            'expired_date' => now()->addYear()->toDateString(),
        ]);

        $response = $this->actingAs($user)->post('/stock-opname/adjust', [
            'product_id' => $product->id,
            'qty_fisik' => 12,
        ]);

        $response->assertOk();
        $response->assertJson(['delta' => -8]);
        $this->assertDatabaseHas('t_productqty', [
            'product_id' => $product->id,
            'type' => 'out',
            'source' => 'opname',
            'qty' => 8,
        ]);
    }

    public function test_adjust_rejects_a_product_from_another_branch(): void
    {
        $user = $this->authorizedUser(klienfk: 1);
        $product = $this->makeProduct(klienfk: 2);

        $response = $this->actingAs($user)->post('/stock-opname/adjust', [
            'product_id' => $product->id,
            'qty_fisik' => 10,
        ]);

        $response->assertStatus(404);
        $this->assertDatabaseMissing('t_productqty', ['product_id' => $product->id]);
    }

    public function test_get_history_only_shows_opname_mutations_from_the_same_branch(): void
    {
        $user = $this->authorizedUser(klienfk: 1);
        $product = $this->makeProduct(klienfk: 1, overrides: ['name' => 'Obat Cabang Saya']);
        $otherProduct = $this->makeProduct(klienfk: 2, overrides: ['name' => 'Obat Cabang Lain']);

        ProductQTY::create([
            'product_id' => $product->id,
            'location_id' => 1,
            'qty' => 5,
            'source' => 'opname',
            'type' => 'in',
            'expired_date' => now()->addYear()->toDateString(),
        ]);
        ProductQTY::create([
            'product_id' => $otherProduct->id,
            'location_id' => 1,
            'qty' => 7,
            'source' => 'opname',
            'type' => 'out',
            'expired_date' => now()->addYear()->toDateString(),
        ]);

        $response = $this->actingAs($user)->post('/stock-opname/getHistory');

        $response->assertOk();
        $names = collect($response->json('data'))->pluck('product_name');
        $this->assertTrue($names->contains($product->name));
        $this->assertFalse($names->contains($otherProduct->name));
    }
}
