<?php

use App\Models\Module;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Repurposes the unused "Feature Locked" placeholder slot (RPC) into the
     * Laporan Pasien & Karyawan menu — same approach as RPA/RPB.
     */
    public function up(): void
    {
        Module::where('code', 'RPC')->update([
            'name' => 'Laporan Pasien & Karyawan',
            'icon' => 'iconoir-group',
            'route' => '/report-klinis',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Module::where('code', 'RPC')->update([
            'name' => '🔐 Feature Locked',
            'icon' => 'iconoir-book-lock',
            'route' => '#',
        ]);
    }
};
