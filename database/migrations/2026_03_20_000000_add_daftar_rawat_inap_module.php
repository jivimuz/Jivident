<?php

use App\Models\Module;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * New sidebar menu "Daftar Rawat Inap" under the existing "Daftar Pasien"
     * group (group_id 4, alongside Ruang Tunggu/DPP, Sudah Diperiksa/DPS,
     * Dibatalkan/DBT, Control Antrian/DPA) — a patient-list view of who's
     * currently in rawat inap, in the same spirit as its siblings in that
     * group. Deliberately a different label from the existing "Rawat Inap"
     * module (code RWI, under "Layanan Lanjutan") to avoid two identically
     * named sidebar entries doing different things — this one is a read list
     * + jump-to-Pemeriksaan, RWI is room/admission/discharge management.
     */
    public function up(): void
    {
        if (! Module::where('code', 'DPR')->exists()) {
            Module::insert([
                'name' => 'Daftar Rawat Inap',
                'code' => 'DPR',
                'group_id' => 4,
                'group_name' => 'DP',
                'list_no' => 3,
                'icon' => 'iconoir-bed',
                'route' => '/pasien-rawat-inap',
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
        Module::where('code', 'DPR')->delete();
    }
};
