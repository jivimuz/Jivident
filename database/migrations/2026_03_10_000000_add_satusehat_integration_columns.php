<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Groundwork for a future SATUSEHAT (Kemenkes interoperability platform)
     * integration — nothing calls these columns yet, no API client exists.
     * `m_klien.ihs_id` already existed since the original schema (dormant,
     * never wired into any controller/view until now) and is exactly
     * SATUSEHAT's "Organization" facility ID — each branch/Klien is expected
     * to be its own registered facility. The columns added here are the
     * other SATUSEHAT reference resources a clinic needs mapped before it
     * can report a visit (Encounter): Practitioner (dokter/user), Location
     * (ruang praktek), and Patient. All nullable and populated later, once
     * the app owner has real SATUSEHAT credentials to sync against.
     */
    public function up(): void
    {
        Schema::table('m_users', function (Blueprint $table) {
            $table->string('satusehat_practitioner_id')->nullable()->after('access');
        });

        Schema::table('m_ruangpraktek', function (Blueprint $table) {
            $table->string('satusehat_location_id')->nullable()->after('fk_dokter');
        });

        Schema::table('m_pasien', function (Blueprint $table) {
            $table->string('satusehat_patient_id')->nullable()->after('nobpjs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_users', function (Blueprint $table) {
            $table->dropColumn('satusehat_practitioner_id');
        });

        Schema::table('m_ruangpraktek', function (Blueprint $table) {
            $table->dropColumn('satusehat_location_id');
        });

        Schema::table('m_pasien', function (Blueprint $table) {
            $table->dropColumn('satusehat_patient_id');
        });
    }
};
