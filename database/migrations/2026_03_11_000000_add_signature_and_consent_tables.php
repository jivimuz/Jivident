<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Simple canvas-based e-signature (image + timestamp), not a certified
     * electronic signature (BSrE/Privy) — that would need a real third-party
     * account, same blocker as BPJS/SATUSEHAT. Decided with the app owner:
     * build the simple version now, revisit certified e-signature later if
     * ever needed.
     */
    public function up(): void
    {
        // Dokter menyimpan tanda tangannya sendiri sekali, dipakai ulang di semua surat
        // yang dia terbitkan. Disimpan di m_employee (bukan m_users) karena semua print
        // surat pasien sudah join ke m_employee lewat t_daftarpasien.dokterfk, bukan m_users.
        Schema::table('m_employee', function (Blueprint $table) {
            $table->string('signature_path')->nullable()->after('accountnumber');
        });

        // Tindakan yang butuh persetujuan tertulis sebelum dilakukan (misal cabut gigi,
        // bedah minor). Default false — admin tandai manual per tindakan yang relevan.
        Schema::table('m_tindakan', function (Blueprint $table) {
            $table->boolean('butuh_consent')->default(false)->after('amount');
        });

        Schema::create('t_informed_consent', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('klienfk')->default(1);
            $table->unsignedBigInteger('fk_daftarpasien');
            $table->unsignedBigInteger('fk_pasien');
            $table->enum('tipe', ['general', 'tindakan']);
            // Hanya diisi kalau tipe = tindakan; snapshot nama tindakan disimpan di
            // kolom tindakan_nama supaya riwayat tidak berubah kalau nama tindakan
            // master diedit/dihapus di kemudian hari.
            $table->unsignedBigInteger('fk_tindakan')->nullable();
            $table->string('tindakan_nama')->nullable();
            // Snapshot teks persetujuan yang ditampilkan ke pasien saat tanda tangan —
            // bukan referensi ke template, supaya riwayat tetap akurat kalau template
            // teks consent diubah di kemudian hari.
            $table->text('isi_consent');
            $table->string('ditandatangani_oleh');
            $table->string('hubungan_dengan_pasien')->default('Pasien Sendiri');
            $table->string('signature_path');
            $table->unsignedBigInteger('fk_userhandler')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('fk_daftarpasien');
            $table->index('fk_pasien');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_informed_consent');

        Schema::table('m_tindakan', function (Blueprint $table) {
            $table->dropColumn('butuh_consent');
        });

        Schema::table('m_employee', function (Blueprint $table) {
            $table->dropColumn('signature_path');
        });
    }
};
