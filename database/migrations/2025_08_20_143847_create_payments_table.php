<?php

use App\Models\Payments;
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
        Schema::create('m_payments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('accountname')->nullable();
            $table->string('accountnumber')->nullable();
            $table->string('image_url')->nullable();
            $table->boolean('is_qr')->default(false);
        });

        Payments::insert([
            [
                'title' => "Cash",
                'image_url' => "https://cdn-icons-png.flaticon.com/512/2489/2489756.png",
                'accountname' => null,
                'accountnumber' => null,
                'is_QR' => 0,
            ],
            [
                'title' => "BCA",
                'image_url' => "https://shl.co.id/wp-content/uploads/2022/11/lg-BCA-1.png",
                'accountname' => "Jivi Muzaqi Guntur",
                'accountnumber' => 3741784325,
                'is_QR' => 0,
            ],

            [
                'title' => "QRIS",
                'accountname' => "Jivimz Payments",
                'accountnumber' => null,
                'image_url' => null,
                'is_qr' => 1,
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_payments');
    }
};
