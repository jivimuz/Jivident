<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_etnis', function (Blueprint $table) {
            $table->id(); // auto increment unsigned
            $table->string('klienfk', 30);
            $table->string('etnis', 30);
            $table->string('reportdisplay', 30);
            $table->timestamps(); // created_at & updated_at
            $table->softDeletes(); // deleted_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_etnis');
    }
};
