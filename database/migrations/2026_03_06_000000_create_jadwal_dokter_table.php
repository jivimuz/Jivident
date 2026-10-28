<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Template jadwal praktek mingguan per dokter — dipakai untuk generate slot
     * booking yang tersedia pada tanggal tertentu (`hari` dicocokkan ke
     * Carbon::dayOfWeek: 0=Minggu ... 6=Sabtu).
     */
    public function up(): void
    {
        Schema::create('t_jadwaldokter', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('klienfk')->default(1);
            $table->unsignedBigInteger('fk_dokter');
            $table->unsignedBigInteger('fk_ruangpraktek');
            $table->unsignedTinyInteger('hari'); // 0=Minggu ... 6=Sabtu
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->unsignedSmallInteger('durasi_slot')->default(30); // menit
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('fk_dokter');
            $table->index('hari');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_jadwaldokter');
    }
};
