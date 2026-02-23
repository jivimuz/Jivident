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
        Schema::create('t_productqty', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receipt_id')->nullable();
            $table->foreignId('product_id');
            $table->foreignId('location_id');
            $table->decimal('qty', 15, 0);
            $table->enum('type', ['in', 'out']);
            $table->date('expired_date');
            $table->timestamps();
            $table->softDeletes();


            $table->index('product_id');
            $table->index('location_id');
            $table->index('type');
            $table->index('qty');
            $table->index('expired_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_q_t_y_s');
    }
};
