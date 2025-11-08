<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_jeniskelamin', function (Blueprint $table) {
            $table->id();
            $table->string('klienfk', 30);
            $table->string('kdkemenkes', 30);
            $table->string('jeniskelamin', 30);
            $table->string('reportdisplay', 30);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_jeniskelamin');
    }
};
