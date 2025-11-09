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
            $table->integer('pegawaifk')->nullable();
            $table->unsignedBigInteger('ruanganfk');
            $table->unsignedBigInteger('kelasfk');
            $table->unsignedBigInteger('pasienfk');
            $table->unsignedBigInteger('jenispembayaranfk');
            $table->dateTime('tglregistrasi');
            $table->dateTime('tglpulang')->nullable();
            $table->enum('statuspasien', ['LAMA', 'BARU'])->default('BARU');
            $table->enum('status', ['Didaftarkan', 'Dipanggil', 'Diperiksa', 'Diranapkan', 'Farmasi', 'Selesai'])->default('Didaftarkan');
            $table->dateTime('tgldipanggil')->nullable();
            $table->string('keluhan', 255);
            $table->integer('asalrujukanfk');
            $table->integer('tipelayananfk');
            $table->string('noantrian', 10);
            $table->unsignedBigInteger('departemenfk');
            $table->enum('statuspembayaran', ['Belum Verifikasi', 'Verifikasi Sebagian', 'Verifikasi', 'Lunas'])->default('Belum Verifikasi');
            $table->string('reportdisplay', 255);
            $table->string('encounter_id', 255)->nullable();
            $table->string('conditionmeninggalkanfaskes_id', 255)->nullable();

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
