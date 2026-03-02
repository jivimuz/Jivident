<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_kelurahan', function (Blueprint $table) {
            $table->bigInteger('id')->nullable(); // bigint unsigned auto_increment
            $table->string('klienfk', 30);
            $table->unsignedBigInteger('provinsifk')->index();
            $table->unsignedBigInteger('kotakabupatenfk')->index();
            $table->unsignedBigInteger('kecamatanfk')->index();
            $table->string('kelurahan', 255)->index();;
            $table->string('kdkelurahan', 30);
            $table->integer('urutan')->nullable();
            $table->timestamps(); // created_at & updated_at
            $table->softDeletes(); // deleted_at

            // Optional relasi
            // $table->foreign('provinsifk')->references('id')->on('provinsi_m')->onDelete('cascade');
            // $table->foreign('kotakabupatenfk')->references('id')->on('kotakabupaten_m')->onDelete('cascade');
            // $table->foreign('kecamatanfk')->references('id')->on('kecamatan_m')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_kelurahan');
    }
};
