<?php

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
        Schema::create('t_appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('klienfk')->default(1);
            $table->unsignedBigInteger('fk_dokter');
            $table->unsignedBigInteger('fk_ruangpraktek');
            $table->unsignedBigInteger('fk_pasien');
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->enum('status', ['booked', 'cancelled', 'completed', 'no_show'])->default('booked');
            $table->string('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('cancelled_by')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('fk_dokter');
            $table->index('fk_pasien');
            $table->index('tanggal');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_appointments');
    }
};
