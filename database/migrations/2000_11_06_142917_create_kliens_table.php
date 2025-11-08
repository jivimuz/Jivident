<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_klien', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('organisasifk');
            $table->string('kodeklinik', 30);
            $table->string('tipe', 255);
            $table->string('nama', 255);
            $table->string('nohp', 255);
            $table->string('email', 255);
            $table->string('alamat', 255);
            $table->string('provinsifk', 255)->nullable();
            $table->string('kotakabupatenfk', 255)->nullable();
            $table->string('kecamatanfk', 255)->nullable();
            $table->string('kelurahanfk', 255)->nullable();
            $table->string('provinsi', 255)->nullable();
            $table->string('kotakabupaten', 255)->nullable();
            $table->string('kecamatan', 255)->nullable();
            $table->string('kelurahan', 255)->nullable();
            $table->string('rt', 255)->nullable();
            $table->string('rw', 255)->nullable();
            $table->string('kodepos', 255)->nullable();
            $table->string('longitude', 255)->nullable();
            $table->string('latitude', 255)->nullable();
            $table->string('ihs_id', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_klien');
    }
};
