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
        Schema::create('m_icdx', function (Blueprint $table) {
            $table->id(); // INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->unsignedInteger('klienfk')->default(1);
            $table->string('name', 250);
            $table->string('code', 100);

            // Mengikuti CI4 type double
            $table->double('tarif_a')->default(0);
            $table->double('tarif_b')->default(0);
            $table->double('tarif_c')->default(0);
            $table->double('tarif_d')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index('code');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_icdx');
    }
};
