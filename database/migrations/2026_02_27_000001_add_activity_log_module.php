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
        if (! Module::where('code', 'LOG')->exists()) {
            Module::insert([
                'name' => 'Activity Log',
                'code' => 'LOG',
                'group_id' => 99,
                'group_name' => 'SET',
                'list_no' => 98,
                'icon' => 'iconoir-clock-rotate-right',
                'route' => '/activity-log',
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
        Module::where('code', 'LOG')->delete();
    }
};
