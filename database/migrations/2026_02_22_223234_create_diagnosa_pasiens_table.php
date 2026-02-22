<?php

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
        Schema::create('t_diagnosapasien', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('klienfk')->default(1);
            $table->unsignedBigInteger('fk_daftarpasien');
            $table->unsignedBigInteger('fk_pasien');
            $table->enum('tipe_diagnosa', ['X', 'IX']);
            $table->unsignedBigInteger('fk_diagnosa');
            $table->unsignedBigInteger('fk_dokter')->nullable();
            $table->unsignedBigInteger('fk_userhandler')->nullable();
            $table->string('diagnosa');
            $table->string('dokter');
            $table->decimal('qty', 20, 0);
            $table->double('price')->default(0);
            $table->double('amount')->default(0);

            $table->timestamps();
            $table->softDeletes();
            $table->index('fk_daftarpasien');
            $table->index('tipe_diagnosa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_diagnosapasien');
    }
};
