<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_kotakabupaten', function (Blueprint $table) {
            $table->bigInteger('id')->nullable(); // bigint unsigned auto_increment
            $table->string('klienfk', 30);
            $table->unsignedBigInteger('provinsifk'); // foreign key provinsi
            $table->string('kotakabupaten', 255);
            $table->string('kdkotakabupaten', 30);
            $table->integer('urutan')->nullable();
            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // deleted_at

            // optional: foreign key ke provinsi_m
            // $table->foreign('provinsifk')->references('id')->on('provinsi_m')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_kotakabupaten');
    }
};
