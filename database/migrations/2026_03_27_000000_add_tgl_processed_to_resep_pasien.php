<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * `is_processed` already existed on t_reseppasien since its original
     * migration but was never read or written anywhere — this adds the
     * timestamp counterpart (same `is_processed` + `tgl_processed` pair
     * t_receiveorders already uses) now that ResepDispensingController
     * actually flips it when a pharmacist verifies & dispenses a resep.
     */
    public function up(): void
    {
        Schema::table('t_reseppasien', function (Blueprint $table) {
            $table->timestamp('tgl_processed')->nullable()->after('is_processed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_reseppasien', function (Blueprint $table) {
            $table->dropColumn('tgl_processed');
        });
    }
};
