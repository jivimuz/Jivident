<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Room-transfer history for a rawat inap admission — e.g. 3 days in a
     * regular room, then moved to VIP 2 days before discharge. A purpose-built
     * table rather than relying on RawatInap's existing LogsActivity trail:
     * RawatInapHelper::calculateBilling() needs structured (kamar, moved-at)
     * data to split the room charge across however many rooms were occupied,
     * which a generic activity-log diff isn't built to be queried for.
     */
    public function up(): void
    {
        Schema::create('t_rawatinap_pindah', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fk_rawatinap');
            $table->unsignedBigInteger('fk_kamar_asal');
            $table->unsignedBigInteger('fk_kamar_tujuan');
            $table->dateTime('waktu_pindah');
            $table->text('catatan')->nullable();
            $table->unsignedBigInteger('fk_userhandler')->nullable();
            $table->timestamps();

            $table->index('fk_rawatinap');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_rawatinap_pindah');
    }
};
