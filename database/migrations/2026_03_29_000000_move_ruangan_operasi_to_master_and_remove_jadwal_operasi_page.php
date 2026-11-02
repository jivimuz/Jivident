<?php

use App\Models\Module;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Follow-up request right after Ruangan Operasi/Jadwal Operasi shipped:
     * (1) move the Ruangan Operasi master-data screen into the "Master"
     * sidebar group (group_id 2) instead of "Layanan Lanjutan" — it's a
     * master-data CRUD screen like Tindakan/ICD/Jadwal Dokter, not a
     * clinical-service module; (2) remove the standalone "Jadwal Operasi"
     * staff page/module entirely — scheduling moved into the Pemeriksaan
     * page's own Operasi tab (doctor schedules it while examining the
     * patient, same as Rawat Inap admission) and the calendar itself moved
     * to a public Monitor board, so there's no page left for this module
     * row to point at.
     */
    public function up(): void
    {
        $maxListNo = (int) Module::where('group_id', 2)->where('isheader', 0)->max('list_no');

        Module::where('code', 'OK')->update([
            'group_id' => 2,
            'group_name' => 'M',
            'list_no' => $maxListNo + 1,
        ]);

        Module::where('code', 'JOP')->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Module::where('code', 'OK')->update([
            'group_id' => 8,
            'group_name' => 'LY',
            'list_no' => 7,
        ]);

        if (! Module::where('code', 'JOP')->exists()) {
            Module::insert([
                'name' => 'Jadwal Operasi',
                'code' => 'JOP',
                'group_id' => 8,
                'group_name' => 'LY',
                'list_no' => 7,
                'icon' => 'iconoir-calendar',
                'route' => '/jadwal-operasi',
                'isheader' => false,
                'isactive' => true,
            ]);
        }
    }
};
