<?php

use App\Models\Module;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rawat inap, kesepakatan: billing akumulasi otomatis (tarif kamar per hari +
     * tindakan + diagnosa + lab + radiologi + resep selama masa rawat).
     *
     * Satu admission (`t_rawatinap`) selalu terhubung ke satu `fk_daftarpasien`
     * yang sudah ada (pasien masuk lewat pendaftaran/IGD biasa dulu, baru
     * diputuskan rawat inap) — bukan alur pendaftaran baru. Tindakan/diagnosa/
     * lab/radiologi/resep selama masa rawat tetap dicatat lewat tab Pemeriksaan
     * yang sudah ada (semua sudah keyed ke `fk_daftarpasien`), jadi tidak perlu
     * duplikasi subsistem itu di sini — rawat inap hanya menambahkan: kamar/bed,
     * admisi (masuk/keluar), catatan harian (ronde), dan akumulasi biaya kamar.
     *
     * `m_kamar`: satu baris = satu unit kamar/bed yang bisa dibooking (bukan
     * "kamar" dengan banyak bed di satu baris) — kalau ada kamar berkapasitas 3
     * bed, dibuat 3 baris terpisah, supaya status kosong/terisi per bed jelas.
     */
    public function up(): void
    {
        Schema::create('m_kamar', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('klienfk')->default(1);
            $table->string('nama');
            $table->string('kelas')->nullable();
            $table->double('tarif_per_hari')->default(0);
            $table->enum('status', ['kosong', 'terisi'])->default('kosong');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('t_rawatinap', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('klienfk')->default(1);
            $table->unsignedBigInteger('fk_daftarpasien');
            $table->unsignedBigInteger('fk_pasien');
            $table->unsignedBigInteger('fk_kamar');
            $table->unsignedBigInteger('fk_dokter_penanggungjawab')->nullable();
            $table->dateTime('tgl_masuk');
            $table->dateTime('tgl_keluar')->nullable();
            $table->enum('status', ['dirawat', 'keluar'])->default('dirawat');
            $table->text('catatan_masuk')->nullable();
            $table->text('ringkasan_pulang')->nullable();
            $table->unsignedBigInteger('fk_userhandler')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('fk_daftarpasien');
            $table->index('fk_pasien');
            $table->index('status');
        });

        Schema::create('t_rawatinap_catatan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fk_rawatinap');
            $table->dateTime('waktu');
            $table->string('suhu')->nullable();
            $table->string('nadi')->nullable();
            $table->string('pernapasan')->nullable();
            $table->string('tensi')->nullable();
            $table->text('catatan');
            $table->unsignedBigInteger('fk_userhandler')->nullable();
            $table->timestamps();

            $table->index('fk_rawatinap');
        });

        if (! Module::where('code', 'RWI')->exists()) {
            Module::insert([
                'name' => 'Rawat Inap',
                'code' => 'RWI',
                'group_id' => 8,
                'group_name' => 'LY',
                'list_no' => 4,
                'icon' => 'iconoir-bed',
                'route' => '/rawat-inap',
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
        Module::where('code', 'RWI')->delete();
        Schema::dropIfExists('t_rawatinap_catatan');
        Schema::dropIfExists('t_rawatinap');
        Schema::dropIfExists('m_kamar');
    }
};
