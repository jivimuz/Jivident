<?php

use App\Models\Module;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jadwal Operasi menunjuk `fk_pasien` (Patient), BUKAN `fk_daftarpasien`
     * — operasi dijadwalkan di muka, seringkali sebelum pasien benar-benar
     * check-in hari itu (sama seperti t_appointments), bukan selalu di
     * kunjungan yang sudah ada seperti admisi Rawat Inap. `fk_daftarpasien`
     * tetap disediakan nullable untuk dihubungkan belakangan kalau operasi
     * itu ternyata bertepatan dengan kunjungan yang sudah terdaftar hari
     * itu juga.
     */
    public function up(): void
    {
        Schema::create('t_jadwal_operasi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('klienfk')->default(1);
            $table->unsignedBigInteger('fk_ruangan_operasi');
            $table->unsignedBigInteger('fk_pasien');
            $table->unsignedBigInteger('fk_dokter');
            $table->unsignedBigInteger('fk_daftarpasien')->nullable();
            $table->string('nama_operasi');
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->enum('status', ['dijadwalkan', 'berlangsung', 'selesai', 'dibatalkan'])->default('dijadwalkan');
            $table->text('catatan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('cancelled_by')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('klienfk');
            $table->index('fk_ruangan_operasi');
            $table->index('fk_dokter');
            $table->index('fk_pasien');
            $table->index('tanggal');
            $table->index('status');
        });

        if (! Module::where('code', 'JOP')->exists()) {
            $maxListNo = (int) Module::where('group_id', 8)->where('isheader', 0)->max('list_no');

            Module::insert([
                'name' => 'Jadwal Operasi',
                'code' => 'JOP',
                'group_id' => 8,
                'group_name' => 'LY',
                'list_no' => $maxListNo + 1,
                'icon' => 'iconoir-calendar',
                'route' => '/jadwal-operasi',
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
        Module::where('code', 'JOP')->delete();
        Schema::dropIfExists('t_jadwal_operasi');
    }
};
