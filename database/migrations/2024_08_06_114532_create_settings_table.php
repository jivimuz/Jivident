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
        Schema::create('m_setting', function (Blueprint $table) {
            $table->id();
            $table->string('parameter');
            $table->string('value')->nullable();
            $table->enum('type', ['text', 'number', 'file'])->default('text');
        });

        Setting::insert([
            [
                "parameter" => 'app_name',
                "value" => 'JiviDent',
                "type" => 'text',
            ],
            [
                "parameter" => 'company_name',
                "value" => 'JiviDent',
                "type" => 'text',
            ],
            [
                "parameter" => 'company_city',
                "value" => 'Cirebon',
                "type" => 'text',
            ],
            [
                "parameter" => 'company_address',
                "value" => 'Cirebon - Jawa Barat',
                "type" => 'text',
            ],
            [
                "parameter" => 'company_logo',
                "value" => null,
                "type" => 'file',
            ],
            [
                "parameter" => 'id_youtube_monitor',
                "value" => '1ZYbU82GVz4',
                "type" => 'text',
            ],
            // [
            //     "parameter" => 'harga_pendaftaran',
            //     "value" => 100000,
            //     "type" => 'number',
            // ],

        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_setting');
    }
};
