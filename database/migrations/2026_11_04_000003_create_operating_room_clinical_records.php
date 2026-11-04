<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_operasi_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('klienfk');
            $table->unsignedBigInteger('jadwal_operasi_id');
            $table->json('preop_checklist')->nullable();
            $table->text('anesthesia_plan')->nullable();
            $table->text('anesthesia_record')->nullable();
            $table->text('procedure_notes')->nullable();
            $table->json('consumables')->nullable();
            $table->json('implants')->nullable();
            $table->text('recovery_notes')->nullable();
            $table->string('recovery_status', 30)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->unique(['klienfk', 'jadwal_operasi_id']);
            $table->index(['klienfk', 'recovery_status']);
        });

        Schema::create('t_operasi_monitoring', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('klienfk');
            $table->unsignedBigInteger('operasi_record_id');
            $table->dateTime('observed_at');
            $table->string('consciousness', 50)->nullable();
            $table->unsignedTinyInteger('pain_scale')->nullable();
            $table->unsignedSmallInteger('systolic')->nullable();
            $table->unsignedSmallInteger('diastolic')->nullable();
            $table->unsignedTinyInteger('pulse')->nullable();
            $table->unsignedTinyInteger('spo2')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('recorded_by')->nullable();
            $table->timestamps();
            $table->index(['klienfk', 'operasi_record_id', 'observed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_operasi_monitoring');
        Schema::dropIfExists('t_operasi_records');
    }
};
