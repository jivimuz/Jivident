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
        Schema::create('t_productlogs', function (Blueprint $table) {
            $table->id();

            // Relasi item
            $table->bigInteger('product_id');

            // Event type biar rapih
            // contoh: 'adjust', 'purchase_in', 'sale_out', 'return_in', 'return_out', 'replacement_in', 'transfer', 'opname'
            $table->string('type', 30)->index();

            // qty perubahan (+ masuk, - keluar)
            $table->decimal('qty_change', 18, 4);

            // snapshot stock sebelum & sesudah (buat audit)
            $table->decimal('stock_before', 18, 4);
            $table->decimal('stock_after', 18, 4);

            // optional: gudang/lokasi
            $table->unsignedBigInteger('warehouse_id')->nullable()->index();

            // referensi dokumen sumber (generic, biar bisa link ke apa aja)
            // contoh: source_type='purchase_return', source_id=123, ref_no='PR-00012'
            $table->string('source_type', 50)->nullable()->index();
            $table->unsignedBigInteger('source_id')->nullable()->index();
            $table->string('ref_no', 50)->nullable()->index();

            // notes
            $table->string('note', 255)->nullable();

            // audit user
            $table->bigInteger('created_by')->nullable();

            $table->timestamps();

            // Index gabungan biar query riwayat cepat
            $table->index(['product_id', 'created_at']);
            $table->index(['source_type', 'source_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_productlogs');
    }
};
