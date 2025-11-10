<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_vitalsign', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('klienfk')->nullable();
            $table->unsignedBigInteger('fk_daftarpasien')->nullable();
            $table->string('siastole', 100)->nullable();
            $table->string('diastole', 100)->nullable();
            $table->string('tinggibadan', 100)->nullable();
            $table->string('beratbadan', 100)->nullable();
            $table->string('suhu', 100)->nullable();
            $table->string('nadi', 100)->nullable();
            $table->string('pernapasan', 100)->nullable();
            $table->string('lingkarperut', 100)->nullable();
            $table->string('reportdisplay', 100)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_vitalsign');
    }
};
