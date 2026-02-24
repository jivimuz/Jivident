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
        Schema::create('t_receiveorders', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('klienfk')->default(1); // Standar multi-klinik/apotek

            $table->string('no_receipt', 100);
            // Info PBF & Faktur
            $table->unsignedBigInteger('supplier_id');
            $table->string('no_faktur', 100);
            $table->date('tgl_faktur');
            $table->date('tgl_jatuh_tempo')->nullable();
            $table->string('no_po', 100)->nullable();

            // Catatan
            $table->text('keterangan')->nullable();

            // Summary Keuangan
            $table->double('total_bruto')->default(0);
            $table->double('diskon_global')->default(0);
            $table->double('ppn_persen')->default(0);
            $table->double('grand_total')->default(0);
            $table->boolean('is_processed')->default(false);
            $table->date('tgl_processed')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexing untuk pencarian cepat
            $table->index('no_receipt');
            $table->index('no_faktur');
            $table->index('supplier_id');
            $table->index('tgl_faktur');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_receiveorders');
    }
};
