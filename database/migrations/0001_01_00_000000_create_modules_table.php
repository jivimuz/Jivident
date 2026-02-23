<?php

use App\Models\Module;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('m_modules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->integer('group_id');
            $table->string('group_name');
            $table->integer('list_no');
            $table->string('icon')->nullable();
            $table->string('route')->default('#')->nullable();
            $table->boolean('isheader');
            $table->boolean('isactive');
        });
        $no = 1;
        $data = [
            //  group 1
            [
                'name' => 'Home',
                'code' => 'DB',
                'group_id' => $no++,
                'group_name' => 'db',
                'list_no' => 1,
                'icon' => 'iconoir-home-simple',
                'route' => '/',
                'isheader' => true,
                'isactive' => true,
            ],

            //  group 2
            [
                'name' => 'Master',
                'code' => 'MT',
                'group_id' => $no,
                'group_name' => 'M',
                'list_no' => 2,
                'icon' => 'iconoir-archive',
                'route' => '#',
                'isheader' => true,
                'isactive' => true,
            ],
            [
                'name' => 'Ruang Praktek',
                'code' => 'MTR',
                'group_id' => $no,
                'group_name' => 'M',
                'list_no' => 1,
                'icon' => 'iconoir-view-structure-up',
                'route' => '/ruang-praktek',
                'isheader' => false,
                'isactive' => true,
            ],
            [
                'name' => 'Akses',
                'code' => 'MTA',
                'group_id' => $no,
                'group_name' => 'M',
                'list_no' => 2,
                'icon' => 'iconoir-key-plus',
                'route' => '/access',
                'isheader' => false,
                'isactive' => true,
            ],
            [
                'name' => 'User',
                'code' => 'MTU',
                'group_id' => $no,
                'group_name' => 'M',
                'list_no' => 3,
                'icon' => 'iconoir-user-badge-check',
                'route' => '/users',
                'isheader' => false,
                'isactive' => true,
            ],
            [
                'name' => 'Tindakan',
                'code' => 'MTT',
                'group_id' => $no,
                'group_name' => 'M',
                'list_no' => 4,
                'icon' => 'iconoir-bookmark-book',
                'route' => '/tindakan',
                'isheader' => false,
                'isactive' => true,
            ],
            [
                'name' => 'Metode Pembayaran',
                'code' => 'MTP',
                'group_id' => $no,
                'group_name' => 'M',
                'list_no' => 5,
                'icon' => 'iconoir-bookmark-book',
                'route' => '/payments',
                'isheader' => false,
                'isactive' => true,
            ],
            [
                'name' => 'ICD IX & X',
                'code' => 'ICD',
                'group_id' => $no,
                'group_name' => 'M',
                'list_no' => 6,
                'icon' => 'iconoir-iris-scan',
                'route' => '/icd',
                'isheader' => false,
                'isactive' => true,
            ],
            [
                'name' => 'Employees',
                'code' => 'MTE',
                'group_id' => $no++,
                'group_name' => 'M',
                'list_no' => 0,
                'icon' => '',
                'route' => '/employees',
                'isheader' => false,
                'isactive' => false,
            ],
            [
                'name' => 'Pasien',
                'code' => 'PA',
                'group_id' => $no,
                'group_name' => 'PA',
                'list_no' => 3,
                'icon' => 'iconoir-home-user',
                'route' => '#',
                'isheader' => true,
                'isactive' => true,
            ],
            [
                'name' => 'Pasien Baru',
                'code' => 'PAT',
                'group_id' => $no,
                'group_name' => 'PA',
                'list_no' => 1,
                'icon' => 'iconoir-user-plus',
                'route' => '/patient/add_patient',
                'isheader' => false,
                'isactive' => true,
            ],
            [
                'name' => 'List Pasien',
                'code' => 'PAD',
                'group_id' => $no++,
                'group_name' => 'PA',
                'list_no' => 2,
                'icon' => 'iconoir-user-scan',
                'route' => '/patient',
                'isheader' => false,
                'isactive' => true,
            ],
            [
                'name' => 'Daftar Pasien',
                'code' => 'DP',
                'group_id' => $no,
                'group_name' => 'DP',
                'list_no' => 4,
                'icon' => 'iconoir-book',
                'route' => '#',
                'isheader' => true,
                'isactive' => true,
            ],
            [
                'name' => 'Ruang Tunggu',
                'code' => 'DPP',
                'group_id' => $no,
                'group_name' => 'DP',
                'list_no' => 1,
                'icon' => 'iconoir-user-square',
                'route' => '/ruang-tunggu',
                'isheader' => false,
                'isactive' => true,
            ],
            [
                'name' => 'Sudah Diperiksa',
                'code' => 'DPS',
                'group_id' => $no,
                'group_name' => 'DP',
                'list_no' => 1,
                'icon' => 'iconoir-user-square',
                'route' => '/sudah-diperiksa',
                'isheader' => false,
                'isactive' => true,
            ],
            [
                'name' => 'Control Antrian',
                'code' => 'DPA',
                'group_id' => $no++,
                'group_name' => 'DP',
                'list_no' => 2,
                'icon' => 'iconoir-arcade',
                'route' => '/antrian',
                'isheader' => false,
                'isactive' => false,
            ],
            [
                'name' => 'Keuangan',
                'code' => 'KU',
                'group_id' => $no,
                'group_name' => 'KU',
                'list_no' => 5,
                'icon' => 'iconoir-credit-card',
                'route' => '/billing',
                'isheader' => true,
                'isactive' => true,
            ],
            [
                'name' => 'Billing',
                'code' => 'BL',
                'group_id' => $no,
                'group_name' => 'KU',
                'list_no' => 1,
                'icon' => 'iconoir-card-wallet',
                'route' => '#',
                'isheader' => false,
                'isactive' => true,
            ],
            [
                'name' => 'Transaksi',
                'code' => 'TR',
                'group_id' => $no++,
                'group_name' => 'KU',
                'list_no' => 2,
                'icon' => 'iconoir-data-transfer-both',
                'route' => '#',
                'isheader' => false,
                'isactive' => true,
            ],
            [
                'name' => 'Farmasi',
                'code' => 'FM',
                'group_id' => $no,
                'group_name' => 'FM',
                'list_no' => 6,
                'icon' => 'iconoir-pharmacy-cross-tag',
                'route' => '#',
                'isheader' => false,
                'isactive' => true,
            ],
            [
                'name' => 'Receive Order',
                'code' => 'RO',
                'group_id' => $no,
                'group_name' => 'FM',
                'list_no' => 1,
                'icon' => 'iconoir-task-list',
                'route' => '/receive-order',
                'isheader' => false,
                'isactive' => true,
            ],
            [
                'name' => 'Produk & Obat',
                'code' => 'PR',
                'group_id' => $no,
                'group_name' => 'FM',
                'list_no' => 2,
                'icon' => 'iconoir-potion',
                'route' => '/product',
                'isheader' => false,
                'isactive' => true,
            ],
            [
                'name' => 'Supplier',
                'code' => 'SUP',
                'group_id' => $no++,
                'group_name' => 'FM',
                'list_no' => 3,
                'icon' => 'iconoir-potion',
                'route' => '/supplier',
                'isheader' => false,
                'isactive' => true,
            ],
            [
                'name' => 'Report',
                'code' => 'RP',
                'group_id' => $no,
                'group_name' => 'RP',
                'list_no' => 7,
                'icon' => 'iconoir-paste-clipboard',
                'route' => '#',
                'isheader' => true,
                'isactive' => true,
            ],
            [
                'name' => '🔐 Feature Locked',
                'code' => 'RPA',
                'group_id' => $no,
                'group_name' => 'RP',
                'list_no' => 1,
                'icon' => 'iconoir-book-lock',
                'route' => '#',
                'isheader' => false,
                'isactive' => true,
            ],
            [
                'name' => '🔐 Feature Locked',
                'code' => 'RPB',
                'group_id' => $no,
                'group_name' => 'RP',
                'list_no' => 1,
                'icon' => 'iconoir-book-lock',
                'route' => '#',
                'isheader' => false,
                'isactive' => true,
            ],
            [
                'name' => '🔐 Feature Locked',
                'code' => 'RPC',
                'group_id' => $no++,
                'group_name' => 'RP',
                'list_no' => 1,
                'icon' => 'iconoir-book-lock',
                'route' => '#',
                'isheader' => false,
                'isactive' => true,
            ],

            [
                'name' => 'Setting',
                'code' => 'SET',
                'group_id' => 99,
                'group_name' => 'SET',
                'list_no' => 99,
                'icon' => 'iconoir-folder-settings',
                'route' => '/setting',
                'isheader' => true,
                'isactive' => true,
            ],
        ];

        Module::insert($data);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_modules');
    }
};
