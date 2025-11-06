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
            $table->string('klienfk', 30);
            $table->string('kodepos', 30)->nullable();
            $table->integer('provinsifk');
            $table->integer('kotakabupatenfk');
            $table->integer('kecamatanfk');
            $table->bigInteger('kelurahanfk');
            $table->string('alamat', 255);
            $table->string('rt', 3);
            $table->string('rw', 3);
            $table->string('kelurahan', 255);
            $table->string('kecamatan', 255);
            $table->string('kotakabupaten', 255);
            $table->string('provinsi', 255);
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
