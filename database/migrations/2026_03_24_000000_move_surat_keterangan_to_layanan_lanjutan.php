<?php

use App\Models\Module;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * "Surat Keterangan" used to be its own sidebar group (group_id 9,
     * header `SRM` + its one child `SRT`/"Surat Keterangan Mandiri") — moved
     * into the existing "Layanan Lanjutan" group (group_id 8, alongside
     * IGD/Lab/Radiologi/Rawat Inap) per request, as a flat item after Rawat
     * Inap. `SRM` itself (route `#`, never checked by any `checkAccess`
     * middleware — only `SRT` is) is deleted outright rather than
     * deactivated, since it's a pure header with nothing under it once
     * `SRT` moves out; left in place it would become the exact "lone
     * header+link" case `CLAUDE.md`'s sidebar-header-gotcha note warns
     * about — a dead `#` link that still renders as clickable.
     */
    public function up(): void
    {
        // isheader=0 only — the group's own header row (`LY`) orders itself
        // among *other headers* on a totally different scale (list_no 8
        // there just means "8th group in the sidebar"), so including it
        // here would inflate this to a nonsensical number instead of the
        // next slot after Rawat Inap's.
        $maxListNo = (int) Module::where('group_id', 8)->where('isheader', 0)->max('list_no');

        Module::where('code', 'SRT')->update([
            'group_id' => 8,
            'group_name' => 'LY',
            'list_no' => $maxListNo + 1,
        ]);

        Module::where('code', 'SRM')->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Module::where('code', 'SRM')->exists()) {
            Module::insert([
                'name' => 'Surat Keterangan',
                'code' => 'SRM',
                'group_id' => 9,
                'group_name' => 'SR',
                'list_no' => 9,
                'icon' => 'iconoir-submit-document',
                'route' => '#',
                'isheader' => true,
                'isactive' => true,
            ]);
        }

        Module::where('code', 'SRT')->update([
            'group_id' => 9,
            'group_name' => 'SR',
            'list_no' => 1,
        ]);
    }
};
