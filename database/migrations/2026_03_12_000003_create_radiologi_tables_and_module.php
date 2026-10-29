<?php

use App\Models\Module;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Radiologi, kesepakatan: versi lengkap (order resmi -> jadwal -> hasil ->
     * laporan terstruktur) — beda dari Lab yang sengaja dibuat simpel.
     */
    public function up(): void
    {
        Schema::create('m_jenispemeriksaanradiologi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('klienfk')->default(1);
            $table->string('nama');
            $table->string('deskripsi')->nullable();
            $table->double('harga')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('t_radiologipasien', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('klienfk')->default(1);
            $table->unsignedBigInteger('fk_daftarpasien');
            $table->unsignedBigInteger('fk_pasien');
            $table->unsignedBigInteger('fk_jenispemeriksaan')->nullable();
            // Snapshot nama/harga saat order dibuat, pola yang sama dengan Lab/consent.
            $table->string('jenis_nama');
            $table->double('harga_snapshot')->default(0);
            $table->text('catatan_klinis')->nullable(); // alasan rujukan dari dokter pengirim
            $table->enum('status', ['diminta', 'terjadwal', 'selesai'])->default('diminta');
            $table->unsignedBigInteger('fk_dokter_pengirim')->nullable();
            $table->dateTime('tgl_diminta');
            $table->dateTime('tgl_jadwal')->nullable();
            $table->string('gambar_path')->nullable();
            $table->text('hasil_interpretasi')->nullable();
            $table->unsignedBigInteger('fk_dokter_radiologi')->nullable();
            $table->dateTime('tgl_hasil')->nullable();
            $table->unsignedBigInteger('fk_userhandler')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('fk_daftarpasien');
            $table->index('fk_pasien');
            $table->index('status');
        });

        if (! Module::where('code', 'RAD')->exists()) {
            Module::insert([
                'name' => 'Radiologi',
                'code' => 'RAD',
                'group_id' => 8,
                'group_name' => 'LY',
                'list_no' => 3,
                'icon' => 'iconoir-xray-view',
                'route' => '/radiologi',
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
        Module::where('code', 'RAD')->delete();
        Schema::dropIfExists('t_radiologipasien');
        Schema::dropIfExists('m_jenispemeriksaanradiologi');
    }
};
