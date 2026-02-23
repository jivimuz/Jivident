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
        // 2. Tabel Detail (Item Obat yang Diterima)
        Schema::create('t_receiveorderdetails', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('klienfk')->default(1);

            $table->unsignedBigInteger('receiveorder_id');

            $table->unsignedBigInteger('product_id');
            $table->string('batch_no', 50);

            $table->date('expired_date');

            $table->decimal('qty', 10, 0);
            $table->string('unit_id', 50)->nullable();
            $table->double('harga_beli')->default(0);
            $table->double('diskon_persen')->default(0);
            $table->double('subtotal')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Indexing
            $table->index('receiveorder_id');
            $table->index('product_id');
            $table->index('batch_no');
            $table->index('expired_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_receiveorderdetails');
    }
};
