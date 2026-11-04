<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_rawatinap_medical_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fk_rawatinap');
            $table->enum('type', ['cppt', 'instruksi_dokter', 'resume_medis', 'surat_kontrol']);
            $table->text('subjective')->nullable();
            $table->text('objective')->nullable();
            $table->text('assessment')->nullable();
            $table->text('plan')->nullable();
            $table->text('instruction')->nullable();
            $table->text('diagnosis_final')->nullable();
            $table->text('procedure_summary')->nullable();
            $table->text('condition_discharge')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->string('follow_up_destination', 255)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['fk_rawatinap', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_rawatinap_medical_records');
    }
};
