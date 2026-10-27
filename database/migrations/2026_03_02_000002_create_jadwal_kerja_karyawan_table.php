<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * One row = one concrete shift occurrence for one employee. jam_masuk/jam_pulang
     * are full DATETIMEs (not just TIME) so a shift that crosses midnight (e.g. masuk
     * Senin 22:00, pulang Selasa 08:00) is represented correctly without a separate
     * "crosses midnight" flag — the date difference encodes it naturally. This also
     * keeps the data shape ready for a future attendance/absensi API (mobile app) to
     * compare actual clock-in/out against a concrete scheduled window.
     */
    public function up(): void
    {
        Schema::create('t_jadwalkerjakaryawan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fk_employee');
            $table->unsignedBigInteger('fk_jadwalkerja')->nullable(); // template shift dipakai, kalau ada
            $table->date('tanggal'); // tanggal kerja (buat grouping/kalender), bukan tanggal jam_masuk
            $table->dateTime('jam_masuk');
            $table->dateTime('jam_pulang');
            $table->string('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('fk_employee');
            $table->index('tanggal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_jadwalkerjakaryawan');
    }
};
