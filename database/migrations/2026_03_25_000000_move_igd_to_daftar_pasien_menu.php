<?php

use App\Models\Module;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * IGD registration now goes through the same shared Pendaftaran Pasien
     * form as every other visit (see `PatientController::processPatient()`'s
     * `triase` handling) instead of its own dedicated card on `/igd` — so the
     * IGD queue belongs alongside the app's other patient-queue screens
     * (Ruang Tunggu/Rawat Jalan/Control Antrian/Daftar Rawat Inap) in the
     * "Daftar Pasien" sidebar group, not tucked away under "Layanan
     * Lanjutan" with Lab/Radiologi/Rawat Inap/Surat Keterangan (which are
     * reached from a different stage of the flow, not from the queue).
     * Inserted right before "Dibatalkan", which stays deliberately last as
     * the exception path (see the Daftar Pasien reordering note in
     * CLAUDE.md) — IGD is a normal entry point, not an exception.
     *
     * Same `checkAccess:IGD` route/module code either way (unchanged) — this
     * only moves where the link is *displayed*, not what gates it.
     */
    public function up(): void
    {
        // isheader=0 only — the group's own header row ("DP") orders itself
        // among *other headers* on a different scale, so including it here
        // would inflate this to a nonsensical number instead of the next
        // slot after Daftar Pasien's last real sibling (see CLAUDE.md).
        $maxListNo = (int) Module::where('group_id', 4)->where('isheader', 0)->max('list_no');

        Module::where('group_id', 4)->where('code', 'DBT')->update([
            'list_no' => $maxListNo + 1,
        ]);

        Module::where('code', 'IGD')->update([
            'group_id' => 4,
            'group_name' => 'DP',
            'list_no' => $maxListNo,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Module::where('code', 'IGD')->update([
            'group_id' => 8,
            'group_name' => 'LY',
            'list_no' => 1,
        ]);

        Module::where('group_id', 4)->where('code', 'DBT')->update([
            'list_no' => 5,
        ]);
    }
};
