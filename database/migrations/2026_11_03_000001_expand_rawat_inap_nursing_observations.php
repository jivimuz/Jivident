<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_rawatinap_catatan', function (Blueprint $table) {
            $table->string('kesadaran', 100)->nullable()->after('tensi');
            $table->string('gcs', 20)->nullable()->after('kesadaran');
            $table->string('spo2', 20)->nullable()->after('gcs');
            $table->string('skala_nyeri', 20)->nullable()->after('spo2');
            $table->decimal('intake_ml', 10, 2)->nullable()->after('skala_nyeri');
            $table->decimal('output_ml', 10, 2)->nullable()->after('intake_ml');
            $table->text('intervensi')->nullable()->after('catatan');
            $table->text('respons')->nullable()->after('intervensi');
        });
    }

    public function down(): void
    {
        Schema::table('t_rawatinap_catatan', function (Blueprint $table) {
            $table->dropColumn([
                'kesadaran',
                'gcs',
                'spo2',
                'skala_nyeri',
                'intake_ml',
                'output_ml',
                'intervensi',
                'respons',
            ]);
        });
    }
};
