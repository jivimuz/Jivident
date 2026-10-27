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
        if (! Module::where('code', 'ROL')->exists()) {
            Module::insert([
                'name' => 'Role',
                'code' => 'ROL',
                'group_id' => 2,
                'group_name' => 'M',
                'list_no' => 7,
                'icon' => 'iconoir-shield-check',
                'route' => '/roles',
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
        Module::where('code', 'ROL')->delete();
    }
};
