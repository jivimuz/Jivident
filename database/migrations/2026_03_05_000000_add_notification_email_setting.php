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
        if (! Setting::where('parameter', 'notification_email')->exists()) {
            Setting::insert([
                'name' => 'Email Notifikasi Operasional',
                'parameter' => 'notification_email',
                'value' => null,
                'type' => 'text',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Setting::where('parameter', 'notification_email')->delete();
    }
};
