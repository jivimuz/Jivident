<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Rates are seeded at 0 on purpose — user chose to set these themselves
     * (BPJS Kesehatan + BPJS Ketenagakerjaan component rates/caps change over
     * time and vary by company policy), editable from the Setting page like
     * `ppn_persen`. Payroll calculations will show Rp0 for any component left at 0
     * until filled in.
     */
    public function up(): void
    {
        $rows = [
            ['name' => 'BPJS Kesehatan - % Perusahaan', 'parameter' => 'bpjs_kesehatan_company_persen'],
            ['name' => 'BPJS Kesehatan - % Karyawan', 'parameter' => 'bpjs_kesehatan_employee_persen'],
            ['name' => 'BPJS Kesehatan - Batas Gaji (Rp)', 'parameter' => 'bpjs_kesehatan_cap'],
            ['name' => 'BPJS JHT - % Perusahaan', 'parameter' => 'bpjs_jht_company_persen'],
            ['name' => 'BPJS JHT - % Karyawan', 'parameter' => 'bpjs_jht_employee_persen'],
            ['name' => 'BPJS JKK - % Perusahaan', 'parameter' => 'bpjs_jkk_persen'],
            ['name' => 'BPJS JKM - % Perusahaan', 'parameter' => 'bpjs_jkm_persen'],
            ['name' => 'BPJS JP - % Perusahaan', 'parameter' => 'bpjs_jp_company_persen'],
            ['name' => 'BPJS JP - % Karyawan', 'parameter' => 'bpjs_jp_employee_persen'],
            ['name' => 'BPJS JP - Batas Gaji (Rp)', 'parameter' => 'bpjs_jp_cap'],
        ];

        foreach ($rows as $row) {
            if (! Setting::where('parameter', $row['parameter'])->exists()) {
                Setting::insert([
                    'name' => $row['name'],
                    'parameter' => $row['parameter'],
                    'value' => 0,
                    'type' => 'number',
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Setting::whereIn('parameter', [
            'bpjs_kesehatan_company_persen', 'bpjs_kesehatan_employee_persen', 'bpjs_kesehatan_cap',
            'bpjs_jht_company_persen', 'bpjs_jht_employee_persen',
            'bpjs_jkk_persen', 'bpjs_jkm_persen',
            'bpjs_jp_company_persen', 'bpjs_jp_employee_persen', 'bpjs_jp_cap',
        ])->delete();
    }
};
