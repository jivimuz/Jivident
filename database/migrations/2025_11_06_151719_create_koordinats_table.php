<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('m_koordinat', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('klienfk');
            $table->string('name', 255);
            $table->string('latitude', 255);
            $table->string('longitude', 255);
            $table->string('reportdisplay', 255)->nullable();
            $table->timestamps(); // otomatis bikin created_at & updated_at
            $table->softDeletes(); // otomatis bikin deleted_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('m_koordinat');
    }
};
