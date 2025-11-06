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
                'name' => 'Akses',
                'code' => 'MTA',
                'group_id' => $no,
                'group_name' => 'M',
                'list_no' => 1,
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
                'list_no' => 1,
                'icon' => 'iconoir-user-badge-check',
                'route' => '/users',
                'isheader' => false,
                'isactive' => true,
            ],
            [
                'name' => 'Employees',
                'code' => 'MTE',
                'group_id' => $no++,
                'group_name' => 'M',
                'list_no' => 1,
                'icon' => '',
                'route' => '/employees',
                'isheader' => false,
                'isactive' => false,
            ],
            [
                'name' => 'Pasien',
                'code' => 'PAT',
                'group_id' => $no++,
                'group_name' => 'PAT',
                'list_no' => 3,
                'icon' => 'iconoir-home-user',
                'route' => '/patient',
                'isheader' => true,
                'isactive' => true,
            ],
            [
                'name' => 'Setting',
                'code' => 'SET',
                'group_id' => 99,
                'group_name' => 'SET',
                'list_no' => 7,
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
