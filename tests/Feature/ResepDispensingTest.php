<?php

namespace Tests\Feature;

use App\Models\DaftarPasien;
use App\Models\Module;
use App\Models\Patient;
use App\Models\Product;
use App\Models\ProductQTY;
use App\Models\ResepPasien;
use App\Models\ResepPasienRacikan;
use App\Models\Serial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Sebelum fitur ini, menulis resep tidak pernah memotong stok sama sekali —
 * ResepDispensingController ("Verifikasi Resep") menutup celah itu, dan
 * saveOrUpdateResep() dilindungi supaya resep yang sudah diserahkan tidak
 * bisa diganti lagi oleh dokter (lihat CLAUDE.md, catatan Hospital-style
 * services/ProductQtyHelper).
 */
class ResepDispensingTest extends TestCase
{
    use RefreshDatabase;

    private function authorizedUser(array $moduleCodes): User
    {
        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addMonth()->toDateString(),
            'last_check' => now(),
        ]);

        $ids = Module::whereIn('code', $moduleCodes)->pluck('id');

        $user = User::first();
        $user->klienfk = 1;
        $user->access = $ids->implode(',');
        $user->save();

        return $user;
    }

    private function encode($id): string
    {
        return base64_encode(base64_encode((string) $id));
    }

    private function makeProduct(int $klienfk, array $overrides = []): Product
    {
        return Product::create(array_merge([
            'klienfk' => $klienfk,
            'code' => 'P-'.uniqid(),
            'name' => 'Obat Resep Test',
        ], $overrides));
    }

    private function stockIn(int $productId, float $qty): void
    {
        ProductQTY::create([
            'product_id' => $productId,
            'location_id' => 1,
            'qty' => $qty,
            'source' => 'receive',
            'type' => 'in',
            'expired_date' => now()->addYear()->toDateString(),
        ]);
    }

    private function makeVisitWithResep(int $klienfk, Product $product, float $qty = 5): DaftarPasien
    {
        $patient = Patient::create([
            'klienfk' => $klienfk, 'norm' => 'RM-'.uniqid(),
            'nik' => (string) random_int(1000000000, 9999999999), 'nama' => 'Pasien Resep',
        ]);

        $visit = DaftarPasien::create([
            'klienfk' => $klienfk,
            'noregistrasi' => 'REG-'.uniqid(),
            'ruanganfk' => 1,
            'pasienfk' => $patient->id,
            'tglregistrasi' => now(),
            'statuspasien' => 'LAMA',
            'status' => 'Di Periksa',
            'noantrian' => 'A1',
            'statuspembayaran' => 'Belum Verifikasi',
            'biaya_registrasi' => 0,
        ]);

        ResepPasien::create([
            'klienfk' => $klienfk,
            'pasien_id' => $patient->id,
            'daftarpasien_id' => $visit->id,
            'type' => 'non-racik',
            'r_num' => 1,
            'obat_id' => $product->id,
            'obat_name' => $product->name,
            'qty' => $qty,
            'harga_satuan' => 1000,
            'unit' => 'Tablet',
            'penggunaan_id' => 1,
            'subtotal' => $qty * 1000,
        ]);

        return $visit;
    }

    public function test_page_requires_access(): void
    {
        $this->authorizedUser(['PA']);
        $user = User::first();

        $response = $this->actingAs($user)->get('/verifikasi-resep');

        $response->assertRedirect(route('error'));
    }

    public function test_worklist_groups_by_visit_and_is_scoped_by_branch(): void
    {
        $user = $this->authorizedUser(['VRS']);
        $product = $this->makeProduct(1);
        $this->stockIn($product->id, 20);
        $visit = $this->makeVisitWithResep(1, $product, 5);

        // Resep cabang lain tidak boleh ikut tampil.
        $otherProduct = $this->makeProduct(2);
        $this->makeVisitWithResep(2, $otherProduct, 3);

        $response = $this->actingAs($user)->post('/verifikasi-resep/getWorklist');

        $response->assertOk();
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals($visit->id, $data[0]['daftarpasien_id']);
        $this->assertEquals(5000, $data[0]['total_harga']);
    }

    public function test_dispense_deducts_stock_and_marks_resep_processed(): void
    {
        $user = $this->authorizedUser(['VRS']);
        $product = $this->makeProduct(1);
        $this->stockIn($product->id, 20);
        $visit = $this->makeVisitWithResep(1, $product, 5);

        $response = $this->actingAs($user)->post("/verifikasi-resep/dispense/{$visit->id}");

        $response->assertOk();
        $this->assertDatabaseHas('t_reseppasien', [
            'daftarpasien_id' => $visit->id,
            'is_processed' => true,
        ]);
        $this->assertDatabaseHas('t_productqty', [
            'product_id' => $product->id,
            'type' => 'out',
            'source' => 'transaction',
            'qty' => 5,
        ]);
    }

    public function test_dispense_deducts_stock_for_each_racikan_ingredient(): void
    {
        $user = $this->authorizedUser(['VRS']);
        $ingredient = $this->makeProduct(1, ['name' => 'Paracetamol Powder']);
        $this->stockIn($ingredient->id, 100);

        $patient = Patient::create(['klienfk' => 1, 'norm' => 'RM-'.uniqid(), 'nik' => (string) random_int(1000000000, 9999999999), 'nama' => 'Pasien Racikan']);
        $visit = DaftarPasien::create([
            'klienfk' => 1, 'noregistrasi' => 'REG-'.uniqid(), 'ruanganfk' => 1, 'pasienfk' => $patient->id,
            'tglregistrasi' => now(), 'statuspasien' => 'LAMA', 'status' => 'Di Periksa', 'noantrian' => 'A1',
            'statuspembayaran' => 'Belum Verifikasi', 'biaya_registrasi' => 0,
        ]);

        $resep = ResepPasien::create([
            'klienfk' => 1, 'pasien_id' => $patient->id, 'daftarpasien_id' => $visit->id,
            'type' => 'racik', 'r_num' => 1, 'nama_racikan' => 'Puyer Demam', 'bentuk' => 'Pulveres (Puyer)',
            'qty_buat' => 10, 'unit' => 'Bungkus', 'penggunaan_id' => 1, 'subtotal' => 15000, 'jasa_racik' => 5000,
        ]);

        ResepPasienRacikan::create([
            'pasien_id' => $patient->id, 'daftarpasien_id' => $visit->id, 'reseppasien_id' => $resep->id,
            'product_id' => $ingredient->id, 'product_name' => $ingredient->name, 'qty' => 10,
        ]);

        $response = $this->actingAs($user)->post("/verifikasi-resep/dispense/{$visit->id}");

        $response->assertOk();
        $this->assertDatabaseHas('t_productqty', [
            'product_id' => $ingredient->id,
            'type' => 'out',
            'qty' => 10,
        ]);
        $this->assertDatabaseHas('t_reseppasien', ['id' => $resep->id, 'is_processed' => true]);
    }

    public function test_dispense_rolls_back_entirely_when_stock_is_insufficient(): void
    {
        $user = $this->authorizedUser(['VRS']);
        $product = $this->makeProduct(1);
        $this->stockIn($product->id, 2); // kurang dari qty resep (5)
        $visit = $this->makeVisitWithResep(1, $product, 5);

        $response = $this->actingAs($user)->post("/verifikasi-resep/dispense/{$visit->id}");

        $response->assertStatus(400);
        $this->assertDatabaseHas('t_reseppasien', [
            'daftarpasien_id' => $visit->id,
            'is_processed' => false,
        ]);
        $this->assertDatabaseMissing('t_productqty', [
            'product_id' => $product->id,
            'type' => 'out',
        ]);
    }

    public function test_dispense_rejects_a_visit_from_another_branch(): void
    {
        $user = $this->authorizedUser(['VRS']);
        $product = $this->makeProduct(2);
        $this->stockIn($product->id, 20);
        $visit = $this->makeVisitWithResep(2, $product, 5);

        $response = $this->actingAs($user)->post("/verifikasi-resep/dispense/{$visit->id}");

        $response->assertStatus(404);
    }

    /**
     * Regresi: saveOrUpdateResep() soft-delete + insert ulang SEMUA resep
     * aktif kunjungan setiap kali dokter menekan Submit — tanpa guard ini,
     * draft baru dokter akan menghapus baris yang stoknya sudah dipotong
     * farmasi, membuat riwayat resep tidak lagi cocok dengan mutasi stok.
     */
    public function test_doctor_cannot_resubmit_resep_once_dispensed(): void
    {
        $user = $this->authorizedUser(['VRS', 'DPP']);
        $product = $this->makeProduct(1);
        $this->stockIn($product->id, 20);
        $visit = $this->makeVisitWithResep(1, $product, 5);

        $this->actingAs($user)->post("/verifikasi-resep/dispense/{$visit->id}")->assertOk();

        $response = $this->actingAs($user)->post("/pemeriksaan/pasien/{$this->encode($visit->id)}/resep/save", [
            'cart' => [[
                'type' => 'non-racik',
                'r_num' => 1,
                'obat_id' => $product->id,
                'obat_name' => $product->name,
                'qty' => 1,
                'harga_satuan' => 1000,
                'satuan' => 'Tablet',
                'penggunaan_id' => 1,
                'subtotal' => 1000,
            ]],
        ]);

        $response->assertStatus(400);
        $this->assertDatabaseHas('t_reseppasien', [
            'daftarpasien_id' => $visit->id,
            'qty' => 5,
            'deleted_at' => null,
        ]);
    }
}
