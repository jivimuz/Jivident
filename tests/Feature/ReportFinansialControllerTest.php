<?php

namespace Tests\Feature;

use App\Models\BillingTransaction;
use App\Models\DaftarPasien;
use App\Models\LabPasien;
use App\Models\Module;
use App\Models\Patient;
use App\Models\Payments;
use App\Models\Product;
use App\Models\ProductQTY;
use App\Models\RadiologiPasien;
use App\Models\ReceiveOrder;
use App\Models\Serial;
use App\Models\Supplier;
use App\Models\TindakanPasien;
use App\Models\Transactions;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportFinansialControllerTest extends TestCase
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
        $user->access = (string) Module::where('code', 'RPB')->value('id');
        $user->save();

        return $user;
    }

    public function test_report_finansial_page_requires_access(): void
    {
        Serial::query()->update([
            'serial_code' => 'ABC123',
            'valid_until' => now()->addMonth()->toDateString(),
            'last_check' => now(),
        ]);
        $user = User::first();
        $user->access = '999999';
        $user->save();

        $response = $this->actingAs($user)->get('/report-finansial');

        $response->assertRedirect(route('error'));
    }

    public function test_piutang_computes_outstanding_balance_per_patient(): void
    {
        $user = $this->authorizedUser();

        $patient = Patient::create([
            'klienfk' => $user->klienfk,
            'norm' => 'RM010',
            'nik' => '3333',
            'nama' => 'Pasien Piutang',
        ]);

        $daftar = DaftarPasien::create([
            'klienfk' => $user->klienfk,
            'noregistrasi' => 'REG-001',
            'ruanganfk' => 1,
            'pasienfk' => $patient->id,
            'tglregistrasi' => now(),
            'statuspasien' => 'BARU',
            'status' => 'Selesai',
            'noantrian' => 'A1',
            'statuspembayaran' => 'Belum Verifikasi',
            'biaya_registrasi' => 20000,
        ]);

        TindakanPasien::create([
            'klienfk' => $user->klienfk,
            'fk_daftarpasien' => $daftar->id,
            'tindakan' => 'Scaling',
            'dokter' => 'drg. A',
            'qty' => 1,
            'amount' => 50000,
        ]);

        Transactions::create([
            'klienfk' => $user->klienfk,
            'no_transaction' => 'TRX-P1',
            'daftarpasien_id' => $daftar->id,
            'transaction_type' => 'in',
            'typecode' => 'BIL',
            'amount' => 30000,
        ]);

        $response = $this->actingAs($user)->post('/report-finansial/getPiutang', [
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->toDateString(),
        ]);

        $response->assertOk();
        $data = $response->json('data');
        $this->assertSame('REG-001', $data[0]['noregistrasi']);
        $this->assertEquals(70000, $data[0]['total_tagihan']);
        $this->assertEquals(30000, $data[0]['total_paid']);
        $this->assertEquals(40000, $data[0]['sisa']);
        $this->assertEquals(40000, $response->json('total_piutang'));
    }

    public function test_piutang_includes_lab_and_radiologi_charges(): void
    {
        $user = $this->authorizedUser();

        $patient = Patient::create([
            'klienfk' => $user->klienfk,
            'norm' => 'RM011',
            'nik' => '3334',
            'nama' => 'Pasien Piutang Lab',
        ]);

        $daftar = DaftarPasien::create([
            'klienfk' => $user->klienfk,
            'noregistrasi' => 'REG-002',
            'ruanganfk' => 1,
            'pasienfk' => $patient->id,
            'tglregistrasi' => now(),
            'statuspasien' => 'BARU',
            'status' => 'Selesai',
            'noantrian' => 'A2',
            'statuspembayaran' => 'Belum Verifikasi',
            'biaya_registrasi' => 0,
        ]);

        LabPasien::create([
            'klienfk' => $user->klienfk, 'fk_daftarpasien' => $daftar->id, 'fk_pasien' => $patient->id,
            'jenis_nama' => 'Gula Darah', 'harga_snapshot' => 25000, 'status' => 'selesai', 'tgl_diminta' => now(),
        ]);
        RadiologiPasien::create([
            'klienfk' => $user->klienfk, 'fk_daftarpasien' => $daftar->id, 'fk_pasien' => $patient->id,
            'jenis_nama' => 'Panoramic', 'harga_snapshot' => 150000, 'status' => 'selesai', 'tgl_diminta' => now(),
        ]);

        $response = $this->actingAs($user)->post('/report-finansial/getPiutang', [
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->toDateString(),
        ]);

        $response->assertOk();
        $data = collect($response->json('data'))->firstWhere('noregistrasi', 'REG-002');
        $this->assertEquals(175000, $data['total_tagihan']);
    }

    public function test_rekap_pembayaran_groups_billing_transactions_by_method(): void
    {
        $user = $this->authorizedUser();
        $cash = Payments::where('title', 'Cash')->first();

        BillingTransaction::create([
            'klienfk' => $user->klienfk,
            'transaction_id' => 1,
            'no_billing' => 'BLX-1',
            'amount' => 50000,
            'fk_payment' => $cash->id,
            'type' => 'in',
        ]);
        BillingTransaction::create([
            'klienfk' => $user->klienfk,
            'transaction_id' => 2,
            'no_billing' => 'BLX-2',
            'amount' => 25000,
            'fk_payment' => $cash->id,
            'type' => 'in',
        ]);

        $response = $this->actingAs($user)->post('/report-finansial/getRekapPembayaran', [
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->toDateString(),
        ]);

        $response->assertOk();
        $data = $response->json('data');
        $this->assertSame('Cash', $data[0]['metode']);
        $this->assertEquals(2, $data[0]['jumlah_transaksi']);
        $this->assertEquals(75000, $data[0]['total']);
    }

    public function test_laba_rugi_breaks_down_by_typecode(): void
    {
        $user = $this->authorizedUser();

        Transactions::create([
            'klienfk' => $user->klienfk,
            'no_transaction' => 'TRX-L1',
            'transaction_type' => 'in',
            'typecode' => 'BIL',
            'amount' => 100000,
        ]);
        Transactions::create([
            'klienfk' => $user->klienfk,
            'no_transaction' => 'TRX-L2',
            'transaction_type' => 'out',
            'typecode' => 'FKT',
            'amount' => 40000,
        ]);

        $response = $this->actingAs($user)->post('/report-finansial/getLabaRugi', [
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->toDateString(),
        ]);

        $response->assertOk();
        $this->assertEquals(100000, $response->json('total_in'));
        $this->assertEquals(40000, $response->json('total_out'));
        $this->assertEquals(60000, $response->json('laba_bersih'));
    }

    public function test_kartu_stok_carries_saldo_awal_and_running_balance(): void
    {
        $user = $this->authorizedUser();

        $product = Product::create([
            'klienfk' => $user->klienfk,
            'code' => 'PRD-STOK',
            'name' => 'Produk Kartu Stok',
        ]);

        ProductQTY::create([
            'product_id' => $product->id,
            'location_id' => 1,
            'qty' => 10,
            'source' => 'receive',
            'type' => 'in',
            'created_at' => now()->startOfMonth()->subDays(5),
        ]);
        ProductQTY::create([
            'product_id' => $product->id,
            'location_id' => 1,
            'qty' => 4,
            'source' => 'transaction',
            'type' => 'out',
            'created_at' => now()->startOfMonth()->addDays(1),
        ]);

        $response = $this->actingAs($user)->post('/report-finansial/getKartuStok', [
            'product_id' => $product->id,
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->toDateString(),
        ]);

        $response->assertOk();
        $this->assertEquals(10, $response->json('saldo_awal'));
        $this->assertEquals(6, $response->json('data.0.saldo'));
    }

    public function test_stok_opname_values_current_stock_by_hpp(): void
    {
        $user = $this->authorizedUser();

        $product = Product::create([
            'klienfk' => $user->klienfk,
            'code' => 'PRD-OPNAME',
            'name' => 'Produk Opname',
            'hpp' => 1000,
        ]);

        ProductQTY::create([
            'product_id' => $product->id,
            'location_id' => 1,
            'qty' => 10,
            'source' => 'receive',
            'type' => 'in',
        ]);

        $response = $this->actingAs($user)->post('/report-finansial/getStokOpname');

        $response->assertOk();
        $data = $response->json('data');
        $this->assertEquals(10, $data[0]['stok']);
        $this->assertEquals(10000, $data[0]['nilai_stok']);
        $this->assertEquals(10000, $response->json('total_nilai_stok'));
    }

    public function test_pembelian_supplier_groups_receive_orders_by_supplier(): void
    {
        $user = $this->authorizedUser();

        $supplier = Supplier::create([
            'klienfk' => $user->klienfk,
            'name' => 'PBF Sehat',
        ]);

        ReceiveOrder::create([
            'klienfk' => $user->klienfk,
            'no_receipt' => 'RO-001',
            'supplier_id' => $supplier->id,
            'no_faktur' => 'FKT-001',
            'tgl_faktur' => now()->toDateString(),
            'grand_total' => 500000,
        ]);

        $response = $this->actingAs($user)->post('/report-finansial/getPembelianSupplier', [
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->toDateString(),
        ]);

        $response->assertOk();
        $data = $response->json('data');
        $this->assertSame('PBF Sehat', $data[0]['supplier']);
        $this->assertEquals(1, $data[0]['jumlah_faktur']);
        $this->assertEquals(500000, $data[0]['total_pembelian']);
    }
}
