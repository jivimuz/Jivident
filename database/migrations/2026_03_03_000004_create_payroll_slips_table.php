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
        Schema::create('t_payroll_slips', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fk_employee');
            $table->string('periode', 7); // format YYYY-MM
            $table->decimal('gaji_pokok', 15, 2);
            $table->decimal('bpjs_kesehatan_company', 15, 2)->default(0);
            $table->decimal('bpjs_kesehatan_employee', 15, 2)->default(0);
            $table->decimal('bpjs_jht_company', 15, 2)->default(0);
            $table->decimal('bpjs_jht_employee', 15, 2)->default(0);
            $table->decimal('bpjs_jkk', 15, 2)->default(0);
            $table->decimal('bpjs_jkm', 15, 2)->default(0);
            $table->decimal('bpjs_jp_company', 15, 2)->default(0);
            $table->decimal('bpjs_jp_employee', 15, 2)->default(0);
            $table->string('ptkp_status', 10)->nullable();
            $table->string('ter_kategori', 1)->nullable();
            $table->decimal('pph21_persen', 5, 2)->default(0);
            $table->decimal('pph21_nominal', 15, 2)->default(0);
            $table->decimal('gaji_bersih', 15, 2)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->unique(['fk_employee', 'periode']);
            $table->index('periode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_payroll_slips');
    }
};
