<?php

use App\Models\ProductLocation;
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
        Schema::create('t_productlocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('klienfk')->default(1);
            $table->string('name');
            $table->string('code')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        ProductLocation::insert([
            ['name' => 'Warehouse', "code" => 'WH']
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_productlocations');
    }
};
