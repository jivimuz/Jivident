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
        // Tabel Transaksi Global (Jurnal Umum Perusahaan)
        Schema::create('t_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('klienfk')->default(1); // Hubungkan ke tabel 'agents' jika ada
            $table->string('no_transaction')->unique();
            $table->foreignId('daftarpasien_id')->nullable(); // Hubungkan ke tabel 'agents' jika ada
            $table->foreignId('pasien_id')->nullable(); // Hubungkan ke tabel 'agents' jika ada
            $table->enum('transaction_type', ['in', 'out']);
            $table->string('typecode', 5)->nullable();
            $table->decimal('amount', 15, 2);
            $table->foreignId('userhandler_id')->nullable(); // Hubungkan ke tabel 'agents' jika ada
            $table->string('notes')->nullable();
            $table->string('attachment_path', 2048)->nullable();
            $table->string('attachment_type', 50)->nullable();
            $table->boolean('is_refund')->default(false);
            $table->timestamps();
            $table->softDeletes();

            // Adding an index to the 'name' column
            $table->index('no_transaction');
            $table->index('transaction_type');
            $table->index('daftarpasien_id');
            $table->index('pasien_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_transactions');
    }
};
