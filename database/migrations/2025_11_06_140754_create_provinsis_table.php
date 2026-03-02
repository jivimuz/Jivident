<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_provinsi', function (Blueprint $table) {
            $table->bigInteger('id')->nullable(); // bigint unsigned auto_increment
            $table->string('klienfk', 30);
            $table->string('provinsi', 30)->index();
            $table->string('kdprovinsi', 255);
            $table->integer('urutan')->nullable();
            $table->string('kodebpjs', 30)->nullable();
            $table->timestamps(); // created_at & updated_at
            $table->softDeletes(); // deleted_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_provinsi');
    }
};
