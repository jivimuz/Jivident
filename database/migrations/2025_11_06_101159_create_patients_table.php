<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_pasien', function (Blueprint $table) {
            $table->id();
            $table->string('klienfk', 30);
            $table->string('norm', 30);
            $table->string('nik', 30);
            $table->string('nama', 255);
            $table->string('umur', 30)->nullable();
            $table->string('nobpjs', 30)->nullable();
            $table->string('nohp', 30)->nullable();
            $table->integer('jeniskelaminfk')->nullable();
            $table->string('tempatlahir', 255)->nullable();
            $table->date('tgllahir')->nullable();
            $table->string('namaayah', 255)->nullable();
            $table->string('namaibu', 255)->nullable();
            $table->integer('fk_religion')->nullable();
            $table->integer('fk_maritalstatus')->nullable();
            $table->string('golongandarah', 255)->nullable();
            $table->string('noasuransilain', 255)->nullable();
            $table->integer('fk_study')->nullable();
            $table->integer('pekerjaanfk')->nullable();
            $table->integer('alamatfk')->nullable();
            $table->integer('kebangsaanfk')->nullable();
            $table->integer('etnisfk')->nullable();
            $table->integer('negarafk')->nullable();
            $table->string('namapenanggungjawab', 255)->nullable();
            $table->string('hubungandenganpasien', 255)->nullable();
            $table->string('nohppenanggungjawab', 255)->nullable();
            $table->string('bahasadikuasai', 255)->nullable();
            $table->string('notelprumah', 255)->nullable();
            $table->string('nomorkk', 255)->nullable();
            $table->string('alergi', 255)->nullable();
            $table->string('catatanpasien', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_pasien');
    }
};
