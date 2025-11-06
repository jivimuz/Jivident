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
        Schema::create('t_cuti', function (Blueprint $table) {
            $table->id();
            $table->string('kode');
            $table->integer('fk_employee');
            $table->integer('fk_jeniscuti');
            $table->date('tglpermohonan');
            $table->string('nopermohonan');
            $table->integer('tambah');
            $table->integer('kurang');
            $table->integer('saldo');
            $table->date('startdate');
            $table->date('enddate');
            $table->date('notes');
            $table->date('alamat');
            $table->enum('status',  ['pending', 'approved', 'rejected'])->default('pending');
            $table->boolean('approvedspv')->default(false);
            $table->boolean('approvedhod')->default(false);
            $table->boolean('approvedhr')->default(false);
            $table->boolean('issakit')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_cuti');
    }
};
