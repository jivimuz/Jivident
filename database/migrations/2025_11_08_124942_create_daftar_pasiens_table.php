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
        Schema::create('t_daftarpasien', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('klienfk');
            $table->string('noregistrasi', 20);
            $table->unsignedBigInteger('dokterfk')->nullable();
            $table->unsignedBigInteger('dokterpenanganfk')->nullable();
            $table->integer('pegawaifk')->nullable();
            $table->unsignedBigInteger('ruanganfk');
            $table->unsignedBigInteger('pasienfk');
            $table->enum('jenispembayaran', ['UMUM', 'BPJS'])->default('UMUM');
            $table->dateTime('tglregistrasi');
            $table->dateTime('tglpulang')->nullable();
            $table->enum('statuspasien', ['LAMA', 'BARU'])->default('BARU');
            $table->enum('status', ['Dalam Antrian', 'Di Periksa',  'Farmasi', 'Payment', 'Selesai', 'Dibatalkan'])->default('Dalam Antrian');
            $table->dateTime('tgldiperiksa')->nullable();
            $table->string('keluhan', 255)->nullable();
            $table->string('noantrian', 10);
            $table->enum('statuspembayaran', ['Belum Verifikasi', 'Verifikasi Sebagian', 'Verifikasi', 'Lunas'])->default('Belum Verifikasi');
            $table->string('kondisi_kepulangan', 255)->nullable();
            $table->string('reportdisplay', 255)->nullable();

            $table->timestamps(); // otomatis buat created_at & updated_at
            $table->softDeletes(); // buat deleted_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_daftarpasien');
    }
};
