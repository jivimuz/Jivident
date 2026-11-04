<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Grants one user every module id that exists in m_modules, by writing them
 * all into that user's comma-separated `access` column (the same format
 * CheckModuleAccess/navbar.blade.php already read it in).
 *
 * Which user gets full access is NOT hardcoded so this can be re-run against
 * a different account without editing this file: pass it via env, e.g.
 *
 *   php artisan db:seed --class=ModuleSeeder
 *   FULL_ACCESS_USER_ID=5 php artisan db:seed --class=ModuleSeeder
 *
 * Defaults to user id 1 (the seeded superadmin) when the env var is unset.
 * Deliberately not wired into DatabaseSeeder — this grants blanket access to
 * every module including ones added later, which should stay an explicit,
 * on-demand action rather than something that runs on every migrate:fresh
 * --seed (including in production).
 */
class ModuleSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->syncAdditionalModules();

        $userId = (int) env('FULL_ACCESS_USER_ID', 1);

        $user = User::find($userId);
        if (! $user) {
            $this->command?->warn("ModuleSeeder: user id {$userId} tidak ditemukan, dilewati.");

            return;
        }

        $moduleIds = Module::pluck('id');
        if ($moduleIds->isEmpty()) {
            $this->command?->warn('ModuleSeeder: tabel m_modules kosong, tidak ada yang diberikan.');

            return;
        }

        $user->access = $moduleIds->implode(',');
        $user->save();

        $this->command?->info("ModuleSeeder: user id {$userId} ({$user->username}) diberi akses ke {$moduleIds->count()} modul.");
    }

    /**
     * Module migrations add menu rows during migrate, but ModuleSeeder is
     * intentionally runnable on an existing database where those migrations
     * may not have been applied yet. Keep this list idempotent so the explicit
     * seeder also makes the current sidebar complete.
     */
    private function syncAdditionalModules(): void
    {
        $modules = [
            ['code' => 'ROL', 'name' => 'Role', 'group_id' => 2, 'group_name' => 'M', 'list_no' => 7, 'icon' => 'iconoir-shield-check', 'route' => '/roles'],
            ['code' => 'JDW', 'name' => 'Jadwal Kerja', 'group_id' => 2, 'group_name' => 'M', 'list_no' => 8, 'icon' => 'iconoir-calendar', 'route' => '/jadwal-kerja'],
            ['code' => 'CUT', 'name' => 'Pengajuan Cuti', 'group_id' => 2, 'group_name' => 'M', 'list_no' => 9, 'icon' => 'iconoir-calendar-minus', 'route' => '/cuti'],
            ['code' => 'JDD', 'name' => 'Jadwal Dokter', 'group_id' => 2, 'group_name' => 'M', 'list_no' => 10, 'icon' => 'iconoir-clock-rotate-right', 'route' => '/jadwal-dokter'],
            ['code' => 'MOD', 'name' => 'Diagnosis Odontogram', 'group_id' => 2, 'group_name' => 'M', 'list_no' => 11, 'icon' => 'iconoir-tooth', 'route' => '/odontogram-diagnosis'],
            ['code' => 'OK', 'name' => 'Ruangan Operasi', 'group_id' => 2, 'group_name' => 'M', 'list_no' => 12, 'icon' => 'iconoir-hospital', 'route' => '/ruangan-operasi'],
            ['code' => 'BKG', 'name' => 'Booking Appointment', 'group_id' => 3, 'group_name' => 'PA', 'list_no' => 4, 'icon' => 'iconoir-calendar-plus', 'route' => '/booking'],
            ['code' => 'DPR', 'name' => 'Daftar Rawat Inap', 'group_id' => 4, 'group_name' => 'DP', 'list_no' => 4, 'icon' => 'iconoir-bed', 'route' => '/pasien-rawat-inap'],
            ['code' => 'BTM', 'name' => 'Biaya Tambahan', 'group_id' => 5, 'group_name' => 'KU', 'list_no' => 3, 'icon' => 'iconoir-coins', 'route' => '/biaya-tambahan'],
            ['code' => 'AKT', 'name' => 'Akuntansi', 'group_id' => 5, 'group_name' => 'KU', 'list_no' => 4, 'icon' => 'iconoir-stats-report', 'route' => '/accounting'],
            ['code' => 'BPJ', 'name' => 'Klaim BPJS', 'group_id' => 5, 'group_name' => 'KU', 'list_no' => 5, 'icon' => 'iconoir-shield-check', 'route' => '/bpjs-claims'],
            ['code' => 'RO', 'name' => 'Receive Order', 'group_id' => 6, 'group_name' => 'FM', 'list_no' => 1, 'icon' => 'iconoir-task-list', 'route' => '/receive_order'],
            ['code' => 'VRS', 'name' => 'Verifikasi Resep', 'group_id' => 6, 'group_name' => 'FM', 'list_no' => 4, 'icon' => 'iconoir-pharmacy-cross-circle', 'route' => '/verifikasi-resep'],
            ['code' => 'SO', 'name' => 'Stock Opname', 'group_id' => 6, 'group_name' => 'FM', 'list_no' => 5, 'icon' => 'iconoir-box', 'route' => '/stock-opname'],
            ['code' => 'PRC', 'name' => 'Procurement', 'group_id' => 6, 'group_name' => 'FM', 'list_no' => 6, 'icon' => 'iconoir-box', 'route' => '/procurement'],
            ['code' => 'LOG', 'name' => 'Activity Log', 'group_id' => 99, 'group_name' => 'SET', 'list_no' => 2, 'icon' => 'iconoir-list', 'route' => '/activity-log'],
            ['code' => 'IGD', 'name' => 'IGD', 'group_id' => 8, 'group_name' => 'LY', 'list_no' => 1, 'icon' => 'iconoir-healthcare', 'route' => '/igd'],
            ['code' => 'LAB', 'name' => 'Laboratorium', 'group_id' => 8, 'group_name' => 'LY', 'list_no' => 2, 'icon' => 'iconoir-test-tube', 'route' => '/lab'],
            ['code' => 'RAD', 'name' => 'Radiologi', 'group_id' => 8, 'group_name' => 'LY', 'list_no' => 3, 'icon' => 'iconoir-xray-view', 'route' => '/radiologi'],
            ['code' => 'RWI', 'name' => 'Rawat Inap', 'group_id' => 8, 'group_name' => 'LY', 'list_no' => 4, 'icon' => 'iconoir-bed', 'route' => '/rawat-inap'],
            ['code' => 'QLT', 'name' => 'Mutu & Insiden', 'group_id' => 7, 'group_name' => 'RP', 'list_no' => 4, 'icon' => 'iconoir-shield-check', 'route' => '/quality'],
        ];

        foreach ($modules as $module) {
            Module::updateOrCreate(
                ['code' => $module['code']],
                $module + ['isheader' => false, 'isactive' => true]
            );
        }

        Module::updateOrCreate(
            ['code' => 'LY'],
            [
                'name' => 'Layanan Lanjutan',
                'group_id' => 8,
                'group_name' => 'LY',
                'list_no' => 8,
                'icon' => 'iconoir-hospital',
                'route' => '#',
                'isheader' => true,
                'isactive' => true,
            ]
        );
    }
}
