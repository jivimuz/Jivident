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
        Schema::create('m_satuanproducts', function (Blueprint $table) {
            $table->id();
            $table->enum('category', ['weight', 'volume']);
            $table->string('base_unit', 3);
            $table->string('name');
            $table->string('code');
            $table->decimal('base_banding', 15, 3);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_satuanproducts');
    }
};
