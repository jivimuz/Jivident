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
        Schema::create('t_reseppasien', function (Blueprint $table) {
            $table->id();
            $table->foreignId('klienfk')->daefault(1);
            $table->foreignId('pasien_id')->index();
            $table->foreignId('daftarpasien_id')->index();
            $table->enum('type', ['non-racik', 'racik']);
            $table->decimal('r_num', 5, 0);
            $table->foreignId('obat_id')->nullable();
            $table->string('obat_name', 100)->nullable();
            $table->string('nama_racikan')->nullable();
            $table->string('bentuk', 25)->nullable();
            $table->decimal('qty', 15, 0)->default(0);
            $table->decimal('qty_buat', 15, 0)->default(0);
            $table->double('harga_satuan')->default(0);
            $table->string('unit', 20)->nullable();
            $table->foreignId('penggunaan_id');
            $table->string('signa', 255)->nullable();
            $table->double('subtotal');
            $table->double('jasa_racik')->default(0);
            $table->boolean('is_processed')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_reseppasien');
    }
};
