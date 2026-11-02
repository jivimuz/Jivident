<?php

use App\Models\Module;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ruangan Operasi — berbeda dari `m_kamar` (Rawat Inap), satu ruangan OK
     * cuma dipakai satu kasus pada satu waktu (bukan beberapa ranjang per
     * ruangan), jadi tidak butuh tabel ranjang terpisah. "Sedang dipakai"
     * dihitung live dari t_jadwal_operasi (status='berlangsung'), bukan
     * kolom status tersimpan di ruangan — status di sini cuma menandai
     * ruangan aktif/maintenance (di luar layanan untuk perbaikan).
     */
    public function up(): void
    {
        Schema::create('m_ruangan_operasi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('klienfk')->default(1);
            $table->string('nama');
            $table->string('kode', 50)->nullable();
            $table->string('lantai', 50)->nullable();
            $table->enum('status', ['tersedia', 'maintenance'])->default('tersedia');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('klienfk');
        });

        if (! Module::where('code', 'OK')->exists()) {
            $maxListNo = (int) Module::where('group_id', 8)->where('isheader', 0)->max('list_no');

            Module::insert([
                'name' => 'Ruangan Operasi',
                'code' => 'OK',
                'group_id' => 8,
                'group_name' => 'LY',
                'list_no' => $maxListNo + 1,
                'icon' => 'iconoir-hospital-circle',
                'route' => '/ruangan-operasi',
                'isheader' => false,
                'isactive' => true,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Module::where('code', 'OK')->delete();
        Schema::dropIfExists('m_ruangan_operasi');
    }
};
