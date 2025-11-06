<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_pekerjaan', function (Blueprint $table) {
            $table->id(); // auto increment, unsigned int(11)
            $table->string('klienfk', 30);
            $table->string('kdkemenkes', 30);
            $table->string('pekerjaan', 30);
            $table->string('reportdisplay', 30);
            $table->timestamps(); // created_at & updated_at
            $table->softDeletes(); // deleted_at
        });
        $pekerjaan = [
            ['klienfk' => '1', 'kdkemenkes' => '0', 'pekerjaan' => 'Tidak bekerja', 'reportdisplay' => 'Tidak bekerja'],
            ['klienfk' => '1', 'kdkemenkes' => '1', 'pekerjaan' => 'PNS', 'reportdisplay' => 'PNS'],
            ['klienfk' => '1', 'kdkemenkes' => '2', 'pekerjaan' => 'TNI/POLRI', 'reportdisplay' => 'TNI/POLRI'],
            ['klienfk' => '1', 'kdkemenkes' => '3', 'pekerjaan' => 'BUMN', 'reportdisplay' => 'BUMN'],
            ['klienfk' => '1', 'kdkemenkes' => '4', 'pekerjaan' => 'Pegawai Swasta/Wirausaha', 'reportdisplay' => 'Pegawai Swasta/Wirausaha'],
            ['klienfk' => '1', 'kdkemenkes' => '5', 'pekerjaan' => 'Lain-lain', 'reportdisplay' => 'Lain-lain'],
        ];

        DB::table('pekerjaan_m')->insert($pekerjaan);
    }

    public function down(): void
    {
        Schema::dropIfExists('m_pekerjaan');
    }
};
