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
        Schema::create('m_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('klienfk')->default(1);

            // Info Dasar
            $table->string('code', 50)->unique();
            $table->string('name', 255);
            $table->decimal('strength', 15, 0)->nullable();
            $table->foreignId('satuanfk')->nullable();
            $table->foreignId('unitfk')->nullable();
            $table->foreignId('kategorifk')->nullable();

            // Atribut / Sifat Obat (Boolean)
            $table->boolean('is_fornas')->default(false);
            $table->boolean('is_generik')->default(false);
            $table->boolean('is_antibiotik')->default(false);
            $table->boolean('is_psikotropika')->default(false);
            $table->boolean('is_narkotika')->default(false);
            $table->boolean('is_kronis')->default(false);

            // Keuangan / Harga
            $table->double('harga_beli')->default(0); // Harga modal dari PBF
            $table->double('hpp')->default(0); // Harga Pokok Penjualan (Bisa sama dgn Harga Beli, atau ditambah PPN/Biaya lain)
            $table->double('harga_jual')->default(0); // Harga akhir ke pasien
            $table->decimal('qty', 15, 0)->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Indexing untuk mempercepat pencarian
            $table->index('code');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_products');
    }
};
