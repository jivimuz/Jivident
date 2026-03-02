<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_alamat', function (Blueprint $table) {
            $table->id();

            // Lebih enak kalau klienfk angka (kalau memang ID)
            // kalau memang string, keep string tapi tetap index
            $table->unsignedBigInteger('klienfk')->default(1)->index();

            $table->string('kodepos', 30)->nullable()->index();

            $table->unsignedInteger('provinsifk')->nullable()->index();
            $table->unsignedInteger('kotakabupatenfk')->nullable()->index();
            $table->unsignedInteger('kecamatanfk')->nullable()->index();
            $table->unsignedBigInteger('kelurahanfk')->nullable()->index();

            $table->string('alamat', 255)->nullable();

            $table->string('rt', 3)->nullable();
            $table->string('rw', 3)->nullable();

            // ini yang biasanya dipakai search, wajib index
            $table->string('kelurahan', 255)->nullable()->index();
            $table->string('kecamatan', 255)->nullable()->index();
            $table->string('kotakabupaten', 255)->nullable()->index();
            $table->string('provinsi', 255)->nullable()->index();

            $table->unsignedBigInteger('pasienfk')->index();

            $table->timestamps();
            $table->softDeletes();

            // Index tambahan yang sering kepake
            $table->index(['klienfk', 'pasienfk']);
        });

        // OPTIONAL: kalau kamu butuh pencarian "mengandung" (%term%)
        // dan DB kamu MySQL InnoDB yang support FULLTEXT:
        // Schema::table('m_alamat', function (Blueprint $table) {
        //     $table->fullText(['alamat', 'kelurahan', 'kecamatan', 'kotakabupaten', 'provinsi']);
        // });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_alamat');
    }
};
