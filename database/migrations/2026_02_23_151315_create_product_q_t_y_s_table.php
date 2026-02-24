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
            $table->unsignedBigInteger('receipt_detail_id')->nullable(); // refer ke t_receiveorderdetails.id
            $table->string('batch_no', 100)->nullable();
            $table->unsignedBigInteger('ref_id')->nullable();   // id dokumen transaksi (penjualan/opname/dll)
            $table->string('ref_type', 50)->nullable();         // receiveorder/sale/opname/return/moving
            $table->foreignId('product_id');
            $table->foreignId('location_id');
            $table->decimal('qty', 15, 0);            // kalau mau pecahan
            $table->enum('source', ["receive", "transaction", "return", "opname", "expired", "moving"]);
            $table->enum('type', ['in', 'out']);
            $table->date('expired_date')->nullable();
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
        Schema::dropIfExists('t_productqty');
    }
};
