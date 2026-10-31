<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Groups `m_kamar` (one row = one bed) under a room (`m_ruangan_inap`) so
     * a room with several beds can be configured and visualized as a unit —
     * "berapa ranjang di ruangan ini" — instead of staff adding each bed one
     * by one with no sense of which beds share a physical room.
     *
     * Rate/class stay mirrored onto each `m_kamar` row rather than removed
     * from it, so `RawatInapHelper::calculateBilling()` — which reads
     * `$kamar->tarif_per_hari` directly — needs no change at all. The room
     * becomes the source of truth going forward (edits to a room cascade to
     * its beds via `RawatInapHelper::updateRuangan()`); `m_kamar`'s own
     * `tarif_per_hari`/`kelas` are now just a synced copy.
     */
    public function up(): void
    {
        Schema::create('m_ruangan_inap', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('klienfk')->default(1);
            $table->string('nama');
            $table->string('kelas')->nullable();
            $table->double('tarif_per_hari')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('m_kamar', function (Blueprint $table) {
            $table->unsignedBigInteger('fk_ruangan')->nullable()->after('klienfk');
            $table->unsignedInteger('nomor_ranjang')->nullable()->after('fk_ruangan');
            $table->index('fk_ruangan');
        });

        // Backfill: setiap baris m_kamar yang sudah ada (dibuat sebelum konsep
        // ruangan ini ada) jadi 1 ruangan tersendiri berisi 1 ranjang, supaya
        // data lama tidak hilang/orphan.
        $existingBeds = DB::table('m_kamar')->whereNull('fk_ruangan')->get();
        foreach ($existingBeds as $bed) {
            $ruanganId = DB::table('m_ruangan_inap')->insertGetId([
                'klienfk' => $bed->klienfk,
                'nama' => $bed->nama,
                'kelas' => $bed->kelas,
                'tarif_per_hari' => $bed->tarif_per_hari,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('m_kamar')->where('id', $bed->id)->update([
                'fk_ruangan' => $ruanganId,
                'nomor_ranjang' => 1,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_kamar', function (Blueprint $table) {
            $table->dropIndex(['fk_ruangan']);
            $table->dropColumn(['fk_ruangan', 'nomor_ranjang']);
        });

        Schema::dropIfExists('m_ruangan_inap');
    }
};
