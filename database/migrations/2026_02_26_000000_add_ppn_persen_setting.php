<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Setting::where('parameter', 'ppn_persen')->exists()) {
            Setting::insert([
                'name' => 'PPN Pembelian (%)',
                'parameter' => 'ppn_persen',
                'value' => 11,
                'type' => 'number',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Setting::where('parameter', 'ppn_persen')->delete();
    }
};
