<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_rawatinap_careplans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fk_rawatinap');
            $table->string('diagnosis', 255);
            $table->text('goal');
            $table->text('interventions');
            $table->text('evaluation')->nullable();
            $table->enum('status', ['aktif', 'teratasi', 'dihentikan'])->default('aktif');
            $table->date('target_date')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('evaluated_by')->nullable();
            $table->dateTime('evaluated_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['fk_rawatinap', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_rawatinap_careplans');
    }
};
