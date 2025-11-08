<?php

use App\Models\MarialStatus;
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
        Schema::create('m_marialstatus', function (Blueprint $table) {
            $table->id();
            $table->string('klienfk', 30);
            $table->string('kdkemenkes', 30);
            $table->string('name', 30);
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
        Schema::dropIfExists('m_marialstatus');
    }
};
