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
            $table->string('nobpjs', 30);
            $table->string('nohp', 30);
            $table->enum('gender', ['-', 'Laki-laki', 'Perempuan'])->default('-');
            $table->string('tempatlahir', 255)->nullable();
            $table->date('tgllahir');
            $table->string('namaayah', 255);
            $table->string('namaibu', 255);
            $table->enum('agama', ['-', 'Islam', 'Kristen', 'Katolik', 'Hindu', 'Budha', 'Lainnya'])->default('-');
            $table->enum('status_perkawinan', ['-', 'Belum Menikah', 'Menikah', 'Duda/Janda'])->default('-');
            $table->string('golongandarah', 255)->nullable();
            $table->string('noasuransilain', 255);
            $table->enum('pendidikan', ['-', 'SD', 'SMP', 'SMA', 'D1', 'D2', 'D3', 'S1', 'S2', 'S3'])->default('-');
            $table->integer('pekerjaanfk');
            $table->integer('alamatfk');
            $table->integer('kebangsaanfk');
            $table->integer('etnisfk');
            $table->integer('negarafk');
            $table->string('namapenanggungjawab', 255);
            $table->string('hubungandenganpasien', 255);
            $table->string('nohppenanggungjawab', 255);
            $table->string('bahasadikuasai', 255)->nullable();
            $table->string('notelprumah', 255)->nullable();
            $table->string('nomorkk', 255)->nullable();
            $table->string('alergi', 255)->nullable();
            $table->string('catatanpasien', 255)->nullable();
            $table->integer('is_diabet')->nullable();
            $table->integer('is_hipertensi')->nullable();
            $table->integer('is_tbc')->nullable();
            $table->integer('is_diare')->nullable();
            $table->integer('is_pnemonea')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_pasien');
    }
};
