<?php

use App\Models\Module;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * The "Setting" module (code SET) was seeded as both the group-99 header
     * AND the only item in that group, so it rendered as a direct clickable
     * link (resources/views/layout/navbar.blade.php only turns a header into
     * a non-clickable collapse-toggle label when the group has >= 1 submenu
     * item — see the `count($submenu) >= 1` branch). Once the Activity Log
     * module (code LOG) was added to the same group_id=99 as a real submenu
     * item, Setting silently stopped being clickable: it became the toggle
     * label, and the submenu only lists isheader=0 rows, so only "Activity
     * Log" appeared underneath it. This adds a dedicated header-only row and
     * demotes Setting to a normal child row, matching every other group's
     * structure (e.g. the "Pasien" group: a header row with route '#' plus
     * separate child rows).
     */
    public function up(): void
    {
        $setting = Module::where('code', 'SET')->first();

        if (! $setting || ! $setting->isheader) {
            return;
        }

        $headerId = Module::insertGetId([
            'name' => 'Pengaturan',
            'code' => 'SETH',
            'group_id' => 99,
            'group_name' => 'SET',
            'list_no' => 99,
            'icon' => 'iconoir-settings',
            'route' => '#',
            'isheader' => true,
            'isactive' => true,
        ]);

        Module::where('code', 'SET')->update([
            'isheader' => false,
            'list_no' => 1,
        ]);

        Module::where('code', 'LOG')->update([
            'list_no' => 2,
        ]);

        User::whereNotNull('access')->get(['id', 'access'])->each(function ($user) use ($headerId, $setting) {
            $ids = array_filter(array_map('trim', explode(',', $user->access)));

            if (in_array((string) $setting->id, $ids, true) && ! in_array((string) $headerId, $ids, true)) {
                $ids[] = (string) $headerId;
                $user->update(['access' => implode(',', $ids)]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $headerId = Module::where('code', 'SETH')->value('id');

        Module::where('code', 'SET')->update([
            'isheader' => true,
            'list_no' => 99,
        ]);

        Module::where('code', 'LOG')->update([
            'list_no' => 98,
        ]);

        if ($headerId) {
            User::whereNotNull('access')->get(['id', 'access'])->each(function ($user) use ($headerId) {
                $ids = array_filter(array_map('trim', explode(',', $user->access)), fn ($id) => $id !== (string) $headerId);
                $user->update(['access' => implode(',', $ids)]);
            });

            Module::where('code', 'SETH')->delete();
        }
    }
};
