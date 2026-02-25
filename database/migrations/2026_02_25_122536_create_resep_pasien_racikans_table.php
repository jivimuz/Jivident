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
        Schema::create('t_reseppasienracikan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pasien_id')->index();
            $table->foreignId('daftarpasien_id')->index();
            $table->foreignId('reseppasien_id')->index();
            $table->foreignId('product_id')->index();
            $table->string('product_name');
            $table->decimal('qty', 10, 0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_reseppasienracikan');
    }
};
