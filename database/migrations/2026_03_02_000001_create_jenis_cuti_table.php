<?php

use App\Models\JenisCuti;
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
        Schema::create('m_jeniscuti', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('issakit')->default(false);
            $table->timestamps();
        });

        JenisCuti::insert([
            ['name' => 'Cuti Tahunan', 'issakit' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sakit', 'issakit' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Izin', 'issakit' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cuti Melahirkan', 'issakit' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Lainnya', 'issakit' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_jeniscuti');
    }
};
