<?php

namespace Tests\Feature;

use App\Models\Module;
use App\Models\Product;
use App\Models\ProductCategories;
use App\Models\SatuanProduct;
use App\Models\Serial;
use App\Models\UnitProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ProductImportControllerTest extends TestCase
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
        $user->access = (string) Module::where('code', 'PR')->value('id');
        $user->save();

        return $user;
    }

    private function uploadCsv(string $content): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'import').'.csv';
        file_put_contents($path, $content);

        return new UploadedFile($path, 'produk.csv', 'text/csv', null, true);
    }

    public function test_imports_valid_rows_successfully(): void
    {
        $user = $this->authorizedUser();
        UnitProduct::create(['name' => 'Tablet', 'code' => 'TAB']);
        SatuanProduct::create(['category' => 'weight', 'base_unit' => 'mg', 'name' => 'Strip', 'code' => 'STR', 'base_banding' => 1]);
        ProductCategories::create(['name' => 'Analgesik']);

        $csv = "code,name,strength,satuan,unit,kategori,harga_beli,hpp,harga_jual,is_fornas,is_generik,is_antibiotik,is_psikotropika,is_narkotika,is_kronis\n".
            "PCT001,Paracetamol 500mg,500,Strip,Tablet,Analgesik,8000,10000,15000,1,1,0,0,0,0\n";

        $response = $this->actingAs($user)->post('/product/import', [
            'file' => $this->uploadCsv($csv),
        ]);

        $response->assertOk();
        $response->assertJsonPath('summary.success', 1);
        $response->assertJsonPath('summary.failed', 0);
        $this->assertDatabaseHas('m_products', [
            'code' => 'PCT001',
            'name' => 'Paracetamol 500mg',
            'is_fornas' => 1,
        ]);
    }

    public function test_reports_error_for_unknown_unit_without_creating_product(): void
    {
        $user = $this->authorizedUser();

        $csv = "code,name,strength,satuan,unit,kategori,harga_beli,hpp,harga_jual,is_fornas,is_generik,is_antibiotik,is_psikotropika,is_narkotika,is_kronis\n".
            "PCT002,Obat Tanpa Unit,100,,UnitTidakAda,,0,0,0,0,0,0,0,0,0\n";

        $response = $this->actingAs($user)->post('/product/import', [
            'file' => $this->uploadCsv($csv),
        ]);

        $response->assertOk();
        $response->assertJsonPath('summary.failed', 1);
        $this->assertStringContainsString('tidak ditemukan', $response->json('details.0.message'));
        $this->assertDatabaseMissing('m_products', ['code' => 'PCT002']);
    }

    public function test_reports_error_for_duplicate_code_within_same_file(): void
    {
        $user = $this->authorizedUser();
        UnitProduct::create(['name' => 'Tablet', 'code' => 'TAB']);

        $csv = "code,name,strength,satuan,unit,kategori,harga_beli,hpp,harga_jual,is_fornas,is_generik,is_antibiotik,is_psikotropika,is_narkotika,is_kronis\n".
            "DUP001,Obat A,100,,Tablet,,0,0,0,0,0,0,0,0,0\n".
            "DUP001,Obat B,100,,Tablet,,0,0,0,0,0,0,0,0,0\n";

        $response = $this->actingAs($user)->post('/product/import', [
            'file' => $this->uploadCsv($csv),
        ]);

        $response->assertOk();
        $response->assertJsonPath('summary.success', 1);
        $response->assertJsonPath('summary.failed', 1);
        $this->assertStringContainsString('duplikat', $response->json('details.1.message'));
    }

    public function test_reports_error_for_code_that_already_exists_in_database(): void
    {
        $user = $this->authorizedUser();
        UnitProduct::create(['name' => 'Tablet', 'code' => 'TAB']);
        Product::create(['klienfk' => 1, 'code' => 'EXIST001', 'name' => 'Sudah Ada', 'strength' => 1]);

        $csv = "code,name,strength,satuan,unit,kategori,harga_beli,hpp,harga_jual,is_fornas,is_generik,is_antibiotik,is_psikotropika,is_narkotika,is_kronis\n".
            "EXIST001,Obat Duplikat DB,100,,Tablet,,0,0,0,0,0,0,0,0,0\n";

        $response = $this->actingAs($user)->post('/product/import', [
            'file' => $this->uploadCsv($csv),
        ]);

        $response->assertOk();
        $response->assertJsonPath('summary.failed', 1);
        $this->assertStringContainsString('sudah dipakai', $response->json('details.0.message'));
    }

    public function test_skips_blank_rows_without_counting_them_as_errors(): void
    {
        $user = $this->authorizedUser();
        UnitProduct::create(['name' => 'Tablet', 'code' => 'TAB']);

        $csv = "code,name,strength,satuan,unit,kategori,harga_beli,hpp,harga_jual,is_fornas,is_generik,is_antibiotik,is_psikotropika,is_narkotika,is_kronis\n".
            "OK001,Obat OK,100,,Tablet,,0,0,0,0,0,0,0,0,0\n".
            ",,,,,,,,,,,,,,\n";

        $response = $this->actingAs($user)->post('/product/import', [
            'file' => $this->uploadCsv($csv),
        ]);

        $response->assertOk();
        $response->assertJsonPath('summary.total', 1);
        $response->assertJsonPath('summary.success', 1);
    }

    public function test_download_template_returns_csv_with_header(): void
    {
        $user = $this->authorizedUser();

        $response = $this->actingAs($user)->get('/product/import/template');

        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        $response->assertSee('code,name,strength', false);
    }
}
