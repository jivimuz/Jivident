<?php

use App\Models\Module;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * POS Apotek is now a standalone fullscreen page (layout/fullscreen,
     * no sidebar) reached from the "Menu Lain" dropdown in
     * topcontent.blade.php — same pattern as Monitor Antrian and Portal
     * Pasien, neither of which has a sidebar `Module` row at all. Keeping
     * "Kasir Apotek" listed in the sidebar too was redundant (and a little
     * odd UX-wise: clicking it from the sidebar lands you on a page with no
     * sidebar to click back into).
     *
     * `isactive = false`, not a delete — `CheckModuleAccess` middleware
     * looks up the module by `code` regardless of `isactive` (that flag only
     * controls whether `navbar.blade.php` lists it), so deleting the row
     * would 403 everyone out of `/pos` via `checkAccess:POS`. This only
     * hides it from the sidebar; access control and the "Menu Lain" link
     * are untouched.
     */
    public function up(): void
    {
        Module::where('code', 'POS')->update(['isactive' => false]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Module::where('code', 'POS')->update(['isactive' => true]);
    }
};
