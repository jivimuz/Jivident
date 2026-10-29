<?php

use App\Models\Module;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * New top-level sidebar group for hospital-style services (IGD now; Lab,
     * Radiologi, Rawat Inap follow in their own migrations) — no existing
     * locked/unused module slot fit this, so this is a genuinely new group
     * (group_id 8, sits between Report=7 and Setting=99).
     */
    public function up(): void
    {
        if (! Module::where('code', 'LY')->exists()) {
            Module::insert([
                'name' => 'Layanan Lanjutan',
                'code' => 'LY',
                'group_id' => 8,
                'group_name' => 'LY',
                'list_no' => 8,
                'icon' => 'iconoir-hospital',
                'route' => '#',
                'isheader' => true,
                'isactive' => true,
            ]);
        }

        if (! Module::where('code', 'IGD')->exists()) {
            Module::insert([
                'name' => 'IGD',
                'code' => 'IGD',
                'group_id' => 8,
                'group_name' => 'LY',
                'list_no' => 1,
                'icon' => 'iconoir-healthcare',
                'route' => '/igd',
                'isheader' => false,
                'isactive' => true,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Module::where('code', 'IGD')->delete();
        Module::where('code', 'LY')->delete();
    }
};
