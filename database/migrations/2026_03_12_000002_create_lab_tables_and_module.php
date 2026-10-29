<?php

use App\Models\Module;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lab workflow, kesepakatan: versi simpel (order dari dokter -> input hasil
     * manual -> selesai), tanpa tahap pengambilan sampel/verifikasi terpisah.
     */
    public function up(): void
    {
        Schema::create('m_jenispemeriksaanlab', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('klienfk')->default(1);
            $table->string('nama');
            $table->string('satuan')->nullable();
            $table->string('nilai_rujukan')->nullable();
            $table->double('harga')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('t_labpasien', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('klienfk')->default(1);
            $table->unsignedBigInteger('fk_daftarpasien');
            $table->unsignedBigInteger('fk_pasien');
            $table->unsignedBigInteger('fk_jenispemeriksaan')->nullable();
            // Snapshot nama/satuan/nilai rujukan/harga saat order dibuat — supaya
            // riwayat hasil lab lama tidak berubah kalau master jenis pemeriksaan
            // diedit/dihapus kemudian (pola yang sama dipakai t_informed_consent).
            $table->string('jenis_nama');
            $table->string('satuan_snapshot')->nullable();
            $table->string('nilai_rujukan_snapshot')->nullable();
            $table->double('harga_snapshot')->default(0);
            $table->string('hasil')->nullable();
            $table->boolean('hasil_abnormal')->default(false);
            $table->enum('status', ['diminta', 'selesai'])->default('diminta');
            $table->unsignedBigInteger('fk_dokter_minta')->nullable();
            $table->unsignedBigInteger('fk_petugas_input')->nullable();
            $table->dateTime('tgl_diminta');
            $table->dateTime('tgl_hasil')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('fk_daftarpasien');
            $table->index('fk_pasien');
            $table->index('status');
        });

        if (! Module::where('code', 'LAB')->exists()) {
            Module::insert([
                'name' => 'Laboratorium',
                'code' => 'LAB',
                'group_id' => 8,
                'group_name' => 'LY',
                'list_no' => 2,
                'icon' => 'iconoir-test-tube',
                'route' => '/lab',
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
        Module::where('code', 'LAB')->delete();
        Schema::dropIfExists('t_labpasien');
        Schema::dropIfExists('m_jenispemeriksaanlab');
    }
};
