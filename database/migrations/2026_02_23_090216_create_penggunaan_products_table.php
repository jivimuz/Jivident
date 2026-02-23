<?php

use App\Models\PenggunaanProduct;
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
        Schema::create('m_penggunaanproducts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        PenggunaanProduct::insert([
            ['name' => '1/2 Jam Sebelum Makan'],
            ['name' => '1/2 Jam Sesudah Makan'],
            ['name' => '1 Jam Sebelum Makan'],
            ['name' => '1 Jam Sesudah Makan'],
            ['name' => '2 Jam Sesudah Makan'],
            ['name' => '2 Jam Sebelum Makan'],
            ['name' => '3 Jam Sesudah Makan'],
            ['name' => '3 Jam Sebelum Makan'],
            ['name' => 'Bangun Tidur Saja'],
            ['name' => 'Sebelum Tidur Saja'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_penggunaanproducts');
    }
};
