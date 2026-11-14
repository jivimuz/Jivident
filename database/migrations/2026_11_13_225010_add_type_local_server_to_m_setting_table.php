<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Setting::where('parameter', 'type_local_server')->exists()) {
            Setting::create([
                "name" => 'Type Local Server',
                "parameter" => 'type_local_server',
                "value" => '0',
                "type" => 'number',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Setting::where('parameter', 'type_local_server')->delete();
    }
};
