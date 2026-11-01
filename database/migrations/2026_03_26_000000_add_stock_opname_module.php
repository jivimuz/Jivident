<?php

use App\Models\Module;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * New "Stock Opname" sidebar entry in the Farmasi group (group_id 6,
     * alongside Receive Order/Produk & Obat/Supplier/Kasir Apotek) — lets
     * pharmacy staff do a physical stock count and reconcile differences via
     * ProductQtyHelper::stockOpnameAdjustTotal(), which already existed
     * (locking, FEFO-lot aware opname methods) but had no controller/route/
     * UI wired up to it at all before this.
     */
    public function up(): void
    {
        // isheader=0 only — see the sidebar-header-gotcha note in
        // CLAUDE.md: a group's own header row orders itself among other
        // headers on an unrelated scale, so including it here would
        // inflate this past the next real slot.
        $maxListNo = (int) Module::where('group_id', 6)->where('isheader', 0)->max('list_no');

        if (! Module::where('code', 'SO')->exists()) {
            Module::insert([
                'name' => 'Stock Opname',
                'code' => 'SO',
                'group_id' => 6,
                'group_name' => 'FM',
                'list_no' => $maxListNo + 1,
                'icon' => 'iconoir-clipboard-check',
                'route' => '/stock-opname',
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
        Module::where('code', 'SO')->delete();
    }
};
