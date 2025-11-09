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
        Schema::create('m_ruangpraktek', function (Blueprint $table) {
            $table->id();
            $table->string('klienfk', 30);
            $table->string('name');
            $table->string('code', 3);
            $table->bigInteger('fk_dokter')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_ruangpraktek');
    }
};
