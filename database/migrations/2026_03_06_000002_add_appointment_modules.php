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
        if (! Module::where('code', 'JDD')->exists()) {
            Module::insert([
                'name' => 'Jadwal Dokter',
                'code' => 'JDD',
                'group_id' => 2,
                'group_name' => 'M',
                'list_no' => 10,
                'icon' => 'iconoir-clock-rotate-right',
                'route' => '/jadwal-dokter',
                'isheader' => false,
                'isactive' => true,
            ]);
        }

        if (! Module::where('code', 'BKG')->exists()) {
            Module::insert([
                'name' => 'Booking Appointment',
                'code' => 'BKG',
                'group_id' => 3,
                'group_name' => 'PA',
                'list_no' => 4,
                'icon' => 'iconoir-calendar-plus',
                'route' => '/booking',
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
        Module::whereIn('code', ['JDD', 'BKG'])->delete();
    }
};
