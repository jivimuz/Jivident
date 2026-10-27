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
        Schema::create('t_possales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('klienfk')->default(1);
            $table->string('no_sale')->unique();
            $table->string('buyer_name')->nullable();
            $table->string('buyer_phone')->nullable();
            $table->unsignedBigInteger('userhandler_id')->nullable();
            $table->unsignedBigInteger('fk_payment')->nullable();
            $table->decimal('total_gross', 15, 2)->default(0);
            $table->decimal('total_discount', 15, 2)->default(0);
            $table->decimal('total_net', 15, 2)->default(0);
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->enum('status', ['completed', 'voided'])->default('completed');
            $table->unsignedBigInteger('refund_transaction_id')->nullable();
            $table->timestamp('voided_at')->nullable();
            $table->unsignedBigInteger('voided_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('no_sale');
            $table->index('klienfk');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_possales');
    }
};
