<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_labpasien', function (Blueprint $table) {
            $table->string('specimen_code', 50)->nullable()->unique();
            $table->dateTime('specimen_collected_at')->nullable();
            $table->unsignedBigInteger('specimen_collected_by')->nullable();
            $table->string('verification_status', 20)->default('pending');
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->dateTime('verified_at')->nullable();
            $table->index(['klienfk', 'verification_status']);
        });

        Schema::table('t_radiologipasien', function (Blueprint $table) {
            $table->string('accession_no', 50)->nullable()->unique();
            $table->string('modality', 50)->nullable();
            $table->string('ruang_pemeriksaan', 100)->nullable();
            $table->string('report_status', 20)->default('draft');
            $table->unsignedBigInteger('report_approved_by')->nullable();
            $table->dateTime('report_approved_at')->nullable();
            $table->index(['klienfk', 'report_status']);
        });
    }

    public function down(): void
    {
        Schema::table('t_labpasien', function (Blueprint $table) {
            $table->dropIndex(['klienfk', 'verification_status']);
            $table->dropUnique(['specimen_code']);
            $table->dropColumn([
                'specimen_code', 'specimen_collected_at', 'specimen_collected_by',
                'verification_status', 'verified_by', 'verified_at',
            ]);
        });

        Schema::table('t_radiologipasien', function (Blueprint $table) {
            $table->dropIndex(['klienfk', 'report_status']);
            $table->dropUnique(['accession_no']);
            $table->dropColumn([
                'accession_no', 'modality', 'ruang_pemeriksaan', 'report_status',
                'report_approved_by', 'report_approved_at',
            ]);
        });
    }
};
