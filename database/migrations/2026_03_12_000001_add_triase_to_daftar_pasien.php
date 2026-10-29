<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Triase IGD, skala 4 warna (Merah/Kuning/Hijau/Hitam) — umum dipakai IGD di
     * Indonesia. Nullable: kunjungan rawat jalan biasa tidak pakai triase sama
     * sekali, kolom ini cuma relevan untuk kunjungan IGD. Disimpan langsung di
     * `t_daftarpasien` (bukan tabel terpisah) karena satu kunjungan cuma punya
     * satu level triase pada satu waktu, dan seluruh alur klinis sesudahnya
     * (vital sign/SOAPE/tindakan/dll) reuse Pemeriksaan yang sudah ada — IGD
     * cuma beda di cara masuk antriannya (prioritas, bukan FIFO), bukan beda
     * flow klinis.
     */
    public function up(): void
    {
        Schema::table('t_daftarpasien', function (Blueprint $table) {
            $table->enum('triase', ['Merah', 'Kuning', 'Hijau', 'Hitam'])->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_daftarpasien', function (Blueprint $table) {
            $table->dropColumn('triase');
        });
    }
};
