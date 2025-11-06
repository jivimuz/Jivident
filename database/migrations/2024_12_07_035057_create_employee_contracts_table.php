<?php

use App\Models\EmployeeContract;
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
        Schema::create('m_employeecontracts', function (Blueprint $table) {
            $table->id();
            $table->integer('fk_employee');
            $table->date('datestart')->nullable()->default(null);
            $table->date('dateend')->nullable()->default(null);
            $table->enum('contracttype', ['Kontrak ke 1', 'Kontrak ke 2', 'Kontrak ke 3', 'Permanent'])->nullable()->default(null);
            $table->enum('contractnature', ['Fixed Contract', 'Open End Contract'])->nullable()->default(null);
            $table->boolean('isrenewal')->nullable()->default(false);
        });

        EmployeeContract::insert([
            "fk_employee" => 1,
            "datestart" => date('Y-m-d', strtotime('-1 Weeks')),
            "dateend" => date('Y-m-d', strtotime('+1 Years')),
            "contracttype" => 'Permanent',
            "contractnature" => 'Open End Contract',
            "isrenewal" => true,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_employeecontracts');
    }
};
