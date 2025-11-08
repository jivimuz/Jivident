<?php

use App\Models\Study;
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
        Schema::create('m_study', function (Blueprint $table) {
            $table->id();
            $table->string('klienfk', 30);
            $table->string('name');
            $table->string('kdkemenkes', 30);
            $table->integer('grade')->nullable();
            $table->string('reportdisplay', 30);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_study');
    }
};
