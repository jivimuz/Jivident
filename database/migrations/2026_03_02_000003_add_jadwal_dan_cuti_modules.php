<?php

use App\Models\Module;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Module::where('code', 'JDW')->exists()) {
            Module::insert([
                'name' => 'Jadwal Kerja',
                'code' => 'JDW',
                'group_id' => 2,
                'group_name' => 'M',
                'list_no' => 8,
                'icon' => 'iconoir-calendar',
                'route' => '/jadwal-kerja',
                'isheader' => false,
                'isactive' => true,
            ]);
        }

        if (! Module::where('code', 'CUT')->exists()) {
            Module::insert([
                'name' => 'Pengajuan Cuti',
                'code' => 'CUT',
                'group_id' => 2,
                'group_name' => 'M',
                'list_no' => 9,
                'icon' => 'iconoir-calendar-minus',
                'route' => '/cuti',
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
        Module::whereIn('code', ['JDW', 'CUT'])->delete();
    }
};
