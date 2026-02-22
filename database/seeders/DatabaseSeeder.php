<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(\Database\Seeders\Pasien\KlienSeeder::class);
        $this->call(\Database\Seeders\Pasien\AgamaSeeder::class);
        $this->call(\Database\Seeders\Pasien\EtnisSeeder::class);
        $this->call(\Database\Seeders\Pasien\JenisKelaminSeeder::class);
        $this->call(\Database\Seeders\Pasien\KebangsaanSeeder::class);
        $this->call(\Database\Seeders\Pasien\ProvinsiSeeder::class);
        $this->call(\Database\Seeders\Pasien\KotaKabupatenSeeder::class);
        $this->call(\Database\Seeders\Pasien\KecamatanSeeder::class);
        $this->call(KelurahanSeeder::class);
        $this->call(PasienSeeder::class);
        $this->call(ICDXSeeder::class);
        $this->call(ICDIXSeeder::class);
        $this->call(\Database\Seeders\Pasien\NegaraSeeder::class);
        $this->call(\Database\Seeders\Pasien\PekerjaanSeeder::class);
        $this->call(\Database\Seeders\Pasien\PendidikanSeeder::class);
        $this->call(\Database\Seeders\Pasien\StatusPerkawinanSeeder::class);
        $this->call(\Database\Seeders\Pasien\KoordinatSeeder::class);
    }
}
