<?php

namespace Tests\Feature;

use App\Models\ICDIX;
use App\Models\JadwalDokter;
use App\Models\Module;
use App\Models\Product;
use App\Models\RuangPraktek;
use App\Models\Serial;
use App\Models\Tindakan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Regresi audit keamanan: edit/update/delete di ProductController,
 * ICDController, TindakanController, SupplierController,
 * RuangPraktekController, dan JadwalDokterController tidak men-scope
 * lookup-by-id ke klienfk — staf dari cabang lain bisa lihat/ubah/hapus
 * master data (produk/obat, ICD, tindakan, supplier, ruang praktek, jadwal
 * dokter) milik cabang yang bukan miliknya.
 */
class MasterDataMultiBranchTest extends TestCase
{
    use RefreshDatabase;

    private function authorizedUser(string $moduleCode, int $klienfk): User
    {
        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addMonth()->toDateString(),
            'last_check' => now(),
        ]);

        $user = User::first();
        $user->klienfk = $klienfk;
        $user->access = (string) Module::where('code', $moduleCode)->value('id');
        $user->save();

        return $user;
    }

    public function test_cannot_delete_a_product_belonging_to_another_branch(): void
    {
        $this->authorizedUser('PR', 1);
        $product = Product::create(['klienfk' => 2, 'code' => 'PRD-X', 'name' => 'Obat Cabang 2']);

        $response = $this->actingAs(User::first())->post("/product/delete/{$product->id}");

        $response->assertStatus(404);
        $this->assertDatabaseHas('m_products', ['id' => $product->id, 'deleted_at' => null]);
    }

    public function test_cannot_edit_an_icd_belonging_to_another_branch(): void
    {
        $this->authorizedUser('ICD', 1);
        $icd = ICDIX::create(['klienfk' => 2, 'code' => 'A00.0', 'name' => 'Diagnosa Cabang 2']);

        $response = $this->actingAs(User::first())->postJson("/icd/updateICDIX/{$icd->id}", [
            'name' => 'Diubah Paksa', 'code' => 'A00.0',
        ]);

        $response->assertStatus(404);
        $this->assertSame('Diagnosa Cabang 2', $icd->refresh()->name);
    }

    public function test_cannot_delete_a_tindakan_belonging_to_another_branch(): void
    {
        $this->authorizedUser('MTT', 1);
        $tindakan = Tindakan::create(['klienfk' => 2, 'name' => 'Tindakan Cabang 2', 'amount' => 50000]);

        $this->actingAs(User::first())->post('/tindakan/deleteTindakan', ['id' => $tindakan->id]);

        $this->assertDatabaseHas('m_tindakan', ['id' => $tindakan->id, 'deleted_at' => null]);
    }

    public function test_new_tindakan_is_attributed_to_the_creating_users_own_branch(): void
    {
        $user = $this->authorizedUser('MTT', 2);

        $this->actingAs($user)->post('/tindakan/saveTindakan', [
            'name' => 'Tindakan Baru', 'amount' => 25000,
        ]);

        $this->assertDatabaseHas('m_tindakan', ['name' => 'Tindakan Baru', 'klienfk' => 2]);
    }

    public function test_cannot_update_a_supplier_belonging_to_another_branch(): void
    {
        $this->authorizedUser('SUP', 1);
        $supplierId = DB::table('m_suppliers')->insertGetId(['klienfk' => 2, 'name' => 'Supplier Cabang 2', 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()]);

        $response = $this->actingAs(User::first())->post("/supplier/update/{$supplierId}", [
            'name' => 'Diubah Paksa',
        ]);

        $response->assertStatus(404);
        $this->assertDatabaseHas('m_suppliers', ['id' => $supplierId, 'name' => 'Supplier Cabang 2']);
    }

    public function test_new_supplier_is_attributed_to_the_creating_users_own_branch(): void
    {
        $user = $this->authorizedUser('SUP', 2);

        $this->actingAs($user)->post('/supplier/store', ['name' => 'Supplier Baru']);

        $this->assertDatabaseHas('m_suppliers', ['name' => 'Supplier Baru', 'klienfk' => 2]);
    }

    public function test_cannot_update_a_ruang_praktek_belonging_to_another_branch(): void
    {
        $this->authorizedUser('MTR', 1);
        $room = RuangPraktek::create(['klienfk' => 2, 'name' => 'Ruang Cabang 2', 'code' => 'RC2']);

        $response = $this->actingAs(User::first())->post('/ruang-praktek/updateRoom', [
            'roomsid' => $room->id, 'roomsname' => 'Diubah Paksa', 'roomsdr' => 1, 'roomscd' => 'XX',
        ]);

        $response->assertStatus(404);
        $this->assertSame('Ruang Cabang 2', $room->refresh()->name);
    }

    public function test_cannot_delete_a_jadwal_dokter_belonging_to_another_branch(): void
    {
        $this->authorizedUser('JDD', 1);
        $room = RuangPraktek::create(['klienfk' => 2, 'name' => 'Ruang Jadwal', 'code' => 'RJ']);
        $jadwal = JadwalDokter::create([
            'klienfk' => 2, 'fk_dokter' => 1, 'fk_ruangpraktek' => $room->id, 'hari' => 1,
            'jam_mulai' => '08:00', 'jam_selesai' => '10:00', 'durasi_slot' => 30, 'is_active' => true,
        ]);

        $response = $this->actingAs(User::first())->post("/jadwal-dokter/delete/{$jadwal->id}");

        $response->assertStatus(404);
        $this->assertDatabaseHas('t_jadwaldokter', ['id' => $jadwal->id, 'deleted_at' => null]);
    }
}
