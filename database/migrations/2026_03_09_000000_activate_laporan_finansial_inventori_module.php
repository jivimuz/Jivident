<?php

use App\Models\Module;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Repurposes the unused "Feature Locked" placeholder slot (RPB) into the
     * Laporan Keuangan & Inventori menu — same approach as RPA (migration
     * 2026_03_04_000000_activate_laporan_module.php).
     */
    public function up(): void
    {
        Module::where('code', 'RPB')->update([
            'name' => 'Laporan Keuangan & Inventori',
            'icon' => 'iconoir-coins',
            'route' => '/report-finansial',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Module::where('code', 'RPB')->update([
            'name' => '🔐 Feature Locked',
            'icon' => 'iconoir-book-lock',
            'route' => '#',
        ]);
    }
};
