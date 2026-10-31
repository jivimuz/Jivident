<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Floor label for a room, free text (Indonesian hospitals/clinics label
     * floors inconsistently — "Lantai 1", "Ground", "Lantai 2 (ICU)" — so this
     * is a plain nullable string, not an enforced integer. Nullable so rooms
     * created before this existed (including ones backfilled by the earlier
     * m_ruangan_inap migration) aren't forced into a floor they were never
     * given — they just won't match any specific floor filter.
     */
    public function up(): void
    {
        Schema::table('m_ruangan_inap', function (Blueprint $table) {
            $table->string('lantai')->nullable()->after('nama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_ruangan_inap', function (Blueprint $table) {
            $table->dropColumn('lantai');
        });
    }
};
