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
        Schema::create('t_billingtransactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('klienfk')->default(1); // Hubungkan ke tabel 'agents' jika ada
            $table->foreignId('transaction_id'); // Hubungkan ke tabel 'agents' jika ada
            $table->string('no_billing', 50); // Hubungkan ke tabel 'agents' jika ada
            $table->foreignId('daftarpasien_id')->nullable(); // Hubungkan ke tabel 'agents' jika ada
            $table->foreignId('pasien_id')->nullable(); // Hubungkan ke tabel 'agents' jika ada
            $table->decimal('amount', 15, 2);
            $table->unsignedBigInteger('fk_payment');
            $table->string('payment_title')->nullable();
            $table->text('payment_content')->nullable(); // Menggunakan TEXT untuk menampung HTML
            $table->string('payment_image_url', 2048)->nullable(); // URL gambar bisa panjang
            $table->boolean('payment_is_qr')->nullable(); // URL gambar bisa panjang
            $table->enum('type', ['in', 'out']);
            $table->timestamps();
            $table->softDeletes();


            $table->index('no_billing');
            $table->index('daftarpasien_id');
            $table->index('pasien_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_billingtransactions');
    }
};
