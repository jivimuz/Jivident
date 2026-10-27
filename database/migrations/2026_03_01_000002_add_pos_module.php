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
        if (! Module::where('code', 'POS')->exists()) {
            Module::insert([
                'name' => 'Kasir Apotek',
                'code' => 'POS',
                'group_id' => 6,
                'group_name' => 'FM',
                'list_no' => 4,
                'icon' => 'iconoir-cart',
                'route' => '/pos',
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
        Module::where('code', 'POS')->delete();
    }
};
