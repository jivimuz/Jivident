<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_alamat', function (Blueprint $table) {
            $table->id(); // INT(11) unsigned auto_increment primary key
            $table->string('klienfk', 30)->default(1);
            $table->string('kodepos', 30)->nullable();
            $table->integer('provinsifk')->nullable();
            $table->integer('kotakabupatenfk')->nullable();
            $table->integer('kecamatanfk')->nullable();
            $table->bigInteger('kelurahanfk')->nullable();
            $table->string('alamat', 255)->nullable();
            $table->string('rt', 3)->nullable();
            $table->string('rw', 3)->nullable();
            $table->string('kelurahan', 255)->nullable();
            $table->string('kecamatan', 255)->nullable();
            $table->string('kotakabupaten', 255)->nullable();
            $table->string('provinsi', 255)->nullable();
            $table->integer('pasienfk');
            $table->timestamps(); // created_at & updated_at
            $table->softDeletes(); // deleted_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_alamat');
    }
};
