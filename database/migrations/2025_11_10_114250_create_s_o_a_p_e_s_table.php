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
        Schema::create('t_soap', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('klienfk')->default(1);
            $table->unsignedBigInteger('fk_daftarpasien')->nullable();
            $table->unsignedBigInteger('pegawaifk')->nullable();
            $table->string('s', 255)->nullable();
            $table->string('o', 255)->nullable();
            $table->string('a', 255)->nullable();
            $table->string('p', 255)->nullable();
            $table->string('e', 255)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_soap');
    }
};
