<?php

use App\Models\Module;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Daftar Pasien submenu reorder (group_id 4) — "Dibatalkan" moved to the
     * very bottom since it's the exception path, not something staff reach
     * for as often as the others; "Sudah Diperiksa" renamed to "Rawat Jalan"
     * to read as the outpatient counterpart to "Daftar Rawat Inap" now that
     * both exist side by side in this same group.
     */
    public function up(): void
    {
        Module::where('code', 'DPP')->update(['list_no' => 1]);
        Module::where('code', 'DPS')->update(['name' => 'Rawat Jalan', 'list_no' => 2]);
        Module::where('code', 'DPA')->update(['list_no' => 3]);
        Module::where('code', 'DPR')->update(['list_no' => 4]);
        Module::where('code', 'DBT')->update(['list_no' => 5]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Module::where('code', 'DPP')->update(['list_no' => 1]);
        Module::where('code', 'DPS')->update(['name' => 'Sudah Diperiksa', 'list_no' => 1]);
        Module::where('code', 'DBT')->update(['list_no' => 1]);
        Module::where('code', 'DPA')->update(['list_no' => 2]);
        Module::where('code', 'DPR')->update(['list_no' => 3]);
    }
};
