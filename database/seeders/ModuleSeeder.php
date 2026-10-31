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
        $userId = (int) env('FULL_ACCESS_USER_ID', 1);

        $user = User::find($userId);
        if (!$user) {
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
}
