<?php

use App\Models\Module;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * New "Verifikasi Resep" sidebar entry in the Farmasi group (group_id 6,
     * alongside Receive Order/Produk & Obat/Supplier/Kasir Apotek/Stock
     * Opname) — the pharmacist-facing worklist where a doctor's resep
     * (written on the Pemeriksaan page) gets verified & dispensed, which
     * deducts stock via ProductQtyHelper::stockOutFefo() and flips
     * t_reseppasien.is_processed. Before this, a resep never touched stock
     * at all — POS and resep were two completely disconnected flows.
     */
    public function up(): void
    {
        // isheader=0 only — see the sidebar-header-gotcha note in
        // CLAUDE.md: a group's own header row orders itself among other
        // headers on an unrelated scale.
        $maxListNo = (int) Module::where('group_id', 6)->where('isheader', 0)->max('list_no');

        if (! Module::where('code', 'VRS')->exists()) {
            Module::insert([
                'name' => 'Verifikasi Resep',
                'code' => 'VRS',
                'group_id' => 6,
                'group_name' => 'FM',
                'list_no' => $maxListNo + 1,
                'icon' => 'iconoir-pharmacy-cross-circle',
                'route' => '/verifikasi-resep',
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
        Module::where('code', 'VRS')->delete();
    }
};
