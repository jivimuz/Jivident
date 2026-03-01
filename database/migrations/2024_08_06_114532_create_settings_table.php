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
            $table->string('name');
            $table->string('parameter');
            $table->string('value')->nullable();
            $table->enum('type', ['text', 'number', 'file'])->default('text');
        });

        Setting::insert([
            [
                "name" => 'App Name',
                "parameter" => 'app_name',
                "value" => 'JiviDent',
                "type" => 'text',
            ],
            [
                "name" => 'Company Name',
                "parameter" => 'company_name',
                "value" => 'JiviDent',
                "type" => 'text',
            ],
            [
                "name" => 'Company City',
                "parameter" => 'company_city',
                "value" => 'Cirebon',
                "type" => 'text',
            ],
            [
                "name" => 'Company Address',
                "parameter" => 'company_address',
                "value" => 'Cirebon - Jawa Barat',
                "type" => 'text',
            ],
            [
                "name" => 'Company Logo',
                "parameter" => 'company_logo',
                "value" => null,
                "type" => 'file',
            ],
            [
                "name" => 'ID Youtube Monitor',
                "parameter" => 'id_youtube_monitor',
                "value" => '1ZYbU82GVz4',
                "type" => 'text',
            ],
            [
                "name" => 'Biaya Pendaftaran Pasien',
                "parameter" => 'patient_registration_price',
                "value" => 50000,
                "type" => 'number',
            ],
            [
                "name" => 'Biaya Jasa Racikan',
                "parameter" => 'patient_jasa_racikan_price',
                "value" => 5000,
                "type" => 'number',
            ],
            [
                "name" => 'Biaya Cetak Surat Keterangan',
                "parameter" => 'patient_surat_keterangan_price',
                "value" => 0,
                "type" => 'number',
            ],


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
