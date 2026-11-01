<?php

namespace Database\Seeders\Master;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        if (Supplier::count() > 0) {
            return;
        }

        $suppliers = [
            ['name' => 'PT Kimia Farma Trading & Distribution', 'address' => 'Jl. Veteran No. 9, Jakarta Pusat', 'phone' => '021-3456789', 'email' => 'order@kimiafarma-td.co.id', 'contact_person' => 'Budi Santoso'],
            ['name' => 'PT Enseval Putera Megatrading', 'address' => 'Jl. Pulo Lentut No. 10, Jakarta Timur', 'phone' => '021-4610570', 'email' => 'sales@enseval.co.id', 'contact_person' => 'Siti Rahmawati'],
            ['name' => 'PT Anugrah Pharmindo Lestari', 'address' => 'Jl. Raya Bekasi Km 27, Bekasi', 'phone' => '021-8841234', 'email' => 'cs@aplpharma.co.id', 'contact_person' => 'Agus Prasetyo'],
            ['name' => 'PT Merapi Utama Pharma', 'address' => 'Jl. Gatot Subroto No. 45, Bandung', 'phone' => '022-7501234', 'email' => 'order@merapipharma.co.id', 'contact_person' => 'Dewi Lestari'],
            ['name' => 'PT Mensa Bina Sukses', 'address' => 'Jl. Soekarno Hatta No. 12, Surabaya', 'phone' => '031-8921234', 'email' => 'distribusi@mensabinasukses.co.id', 'contact_person' => 'Hendra Wijaya'],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create(array_merge(['klienfk' => 1, 'is_active' => true], $supplier));
        }
    }
}
