<?php

use App\Models\Serial;
use Carbon\Carbon;
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
        Schema::create('serial', function (Blueprint $table) {
            $table->id();
            $table->string('serial_code')->nullable();
            $table->datetime('last_check')->nullable();
            $table->date('valid_until')->nullable();
            $table->timestamps();
        });

        Serial::insert([
            "serial_code" => NULL,
            "valid_until" => NULL,
            "created_at" => Carbon::now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('serial');
    }
};
