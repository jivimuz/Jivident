<?php

namespace Tests\Unit;

use App\Helpers\PayrollHelper;
use App\Models\Employee;
use App\Models\Ptkp;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class PayrollHelperTest extends TestCase
{
    use RefreshDatabase;

    private PayrollHelper $helper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->helper = app(PayrollHelper::class);
    }

    private function setBpjsRates(array $overrides = []): void
    {
        $defaults = [
            'bpjs_kesehatan_company_persen' => 4,
            'bpjs_kesehatan_employee_persen' => 1,
            'bpjs_kesehatan_cap' => 12000000,
            'bpjs_jht_company_persen' => 3.7,
            'bpjs_jht_employee_persen' => 2,
            'bpjs_jkk_persen' => 0.24,
            'bpjs_jkm_persen' => 0.3,
            'bpjs_jp_company_persen' => 2,
            'bpjs_jp_employee_persen' => 1,
            'bpjs_jp_cap' => 10042300,
        ];

        foreach (array_merge($defaults, $overrides) as $param => $value) {
            Setting::where('parameter', $param)->update(['value' => $value]);
        }
    }

    public function test_bpjs_kesehatan_respects_salary_cap(): void
    {
        $this->setBpjsRates();

        // gaji 20jt, cap kesehatan 12jt -> dasar hitung tetap 12jt
        $result = $this->helper->calculateBpjs(20000000);

        $this->assertSame(480000.0, $result['bpjs_kesehatan_company']); // 12jt * 4%
        $this->assertSame(120000.0, $result['bpjs_kesehatan_employee']); // 12jt * 1%
    }

    public function test_bpjs_jht_uses_full_salary_without_cap(): void
    {
        $this->setBpjsRates();

        $result = $this->helper->calculateBpjs(20000000);

        $this->assertSame(740000.0, $result['bpjs_jht_company']); // 20jt * 3.7%, tanpa cap
        $this->assertSame(400000.0, $result['bpjs_jht_employee']); // 20jt * 2%
    }

    public function test_bpjs_components_are_zero_when_rates_not_configured(): void
    {
        // tidak panggil setBpjsRates() -> semua masih default 0 dari migration
        $result = $this->helper->calculateBpjs(10000000);

        $this->assertSame(0.0, $result['bpjs_kesehatan_company']);
        $this->assertSame(0.0, $result['bpjs_jht_employee']);
    }

    public function test_pph21_looks_up_correct_ter_bracket_for_ptkp_category_a(): void
    {
        $employee = Employee::create([
            'fullname' => 'Test TK0',
            'nik' => '001',
            'fk_ptkp' => Ptkp::where('name', 'TK0')->value('id'), // kategori A
        ]);

        // 10.200.000 jatuh di bracket A: >10.050.000 s.d. 10.350.000 -> 2.25%
        $result = $this->helper->calculatePph21($employee, 10200000);

        $this->assertSame('A', $result['ter_kategori']);
        $this->assertSame(2.25, $result['pph21_persen']);
        $this->assertSame(229500.0, $result['pph21_nominal']); // 10.200.000 * 2.25%
    }

    public function test_pph21_is_zero_below_ptkp_threshold(): void
    {
        $employee = Employee::create([
            'fullname' => 'Test TK0 Low Income',
            'nik' => '002',
            'fk_ptkp' => Ptkp::where('name', 'TK0')->value('id'),
        ]);

        // di bawah 5.400.000 -> bracket pertama kategori A -> 0%
        $result = $this->helper->calculatePph21($employee, 4000000);

        $this->assertSame(0.0, $result['pph21_persen']);
        $this->assertSame(0.0, $result['pph21_nominal']);
    }

    public function test_pph21_uses_higher_ter_category_for_more_dependents(): void
    {
        $employee = Employee::create([
            'fullname' => 'Test K3',
            'nik' => '003',
            'fk_ptkp' => Ptkp::where('name', 'K3')->value('id'), // kategori C
        ]);

        // 10.200.000 di kategori C masih di bracket >9.800.000 s.d 10.950.000 -> 1.5%
        // (lebih rendah dari kategori A di income yang sama, karena PTKP K3 lebih besar)
        $result = $this->helper->calculatePph21($employee, 10200000);

        $this->assertSame('C', $result['ter_kategori']);
        $this->assertSame(1.5, $result['pph21_persen']);
    }

    public function test_pph21_throws_when_employee_has_no_ptkp(): void
    {
        $employee = Employee::create([
            'fullname' => 'No PTKP',
            'nik' => '004',
            'fk_ptkp' => null,
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->helper->calculatePph21($employee, 10000000);
    }

    public function test_generate_slip_saves_full_breakdown_and_blocks_duplicate_periode(): void
    {
        $this->setBpjsRates();

        $employee = Employee::create([
            'fullname' => 'Slip Test',
            'nik' => '005',
            'fk_ptkp' => Ptkp::where('name', 'TK0')->value('id'),
            'gajipokok' => 10000000,
        ]);

        $slip = $this->helper->generateSlip($employee, '2026-03');

        $this->assertSame('2026-03', $slip->periode);
        $this->assertSame(10000000.0, (float) $slip->gaji_pokok);
        $this->assertLessThan(10000000, (float) $slip->gaji_bersih);

        $this->expectException(InvalidArgumentException::class);
        $this->helper->generateSlip($employee, '2026-03');
    }
}
