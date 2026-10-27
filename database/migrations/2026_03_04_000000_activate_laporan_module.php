<?php

use App\Models\Module;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Repurposes the unused "Feature Locked" placeholder slot (RPA) under the
     * existing "Report" module group into the real Laporan & Analitik menu —
     * confirmed with the app owner these placeholders were just old unused
     * slots, not part of a premium/locked-feature system.
     */
    public function up(): void
    {
        Module::where('code', 'RPA')->update([
            'name' => 'Laporan & Analitik',
            'icon' => 'iconoir-graph-up',
            'route' => '/report',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Module::where('code', 'RPA')->update([
            'name' => '🔐 Feature Locked',
            'icon' => 'iconoir-book-lock',
            'route' => '#',
        ]);
    }
};
