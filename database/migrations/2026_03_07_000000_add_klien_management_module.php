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
        if (! Module::where('code', 'CAB')->exists()) {
            Module::insert([
                'name' => 'Cabang Klinik',
                'code' => 'CAB',
                'group_id' => 2,
                'group_name' => 'M',
                'list_no' => 11,
                'icon' => 'iconoir-building',
                'route' => '/klien',
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
        Module::where('code', 'CAB')->delete();
    }
};
