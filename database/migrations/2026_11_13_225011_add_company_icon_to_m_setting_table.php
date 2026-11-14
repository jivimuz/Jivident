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
        if (!Setting::where('parameter', 'company_icon')->exists()) {
            Setting::create([
                "name" => 'Company Icon',
                "parameter" => 'company_icon',
                "value" => null,
                "type" => 'file',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Setting::where('parameter', 'company_icon')->delete();
    }
};
