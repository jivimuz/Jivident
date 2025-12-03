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
        Schema::create('t_tindakanpasien', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('klienfk')->default(1);
            $table->unsignedBigInteger('fk_daftarpasien')->nullable();
            $table->unsignedBigInteger('fk_tindakan')->nullable();
            $table->unsignedBigInteger('fk_dokter')->nullable();
            $table->unsignedBigInteger('fk_userhandler')->nullable();
            $table->string('tindakan');
            $table->string('dokter');
            $table->decimal('qty', 20, 0);
            $table->double('amount')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_tindakanpasien');
    }
};
