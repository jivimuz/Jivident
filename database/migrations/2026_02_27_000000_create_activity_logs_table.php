<?php

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
        Schema::create('t_activitylogs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('klienfk')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->enum('action', ['created', 'updated', 'deleted']);
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->json('changes')->nullable();
            $table->string('url', 2048)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index('klienfk');
            $table->index('user_id');
            $table->index(['model_type', 'model_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_activitylogs');
    }
};
