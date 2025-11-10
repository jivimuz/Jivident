<?php

use App\Models\Employee;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('m_employee', function (Blueprint $table) {
            $table->id();
            $table->string('fullname');
            $table->string('nik')->unique();
            $table->string('birthplace')->nullable();
            $table->date('birthdate')->nullable();
            $table->enum('gender', ["-", "LAKI-LAKI", "PEREMPUAN"])->default("-");
            $table->string('personalemail')->nullable();
            $table->string('phonenumber')->nullable();
            $table->integer('fk_joblevel')->default(5)->nullable();
            $table->integer('fk_jobtype')->default(1);
            $table->date('joindate')->nullable();
            $table->date('outdate')->nullable();
            $table->integer('fk_designation')->default(1)->nullable();
            $table->integer('fk_study')->nullable();
            $table->integer('fk_marialstatus')->nullable();
            $table->integer('fk_religion')->nullable();
            $table->integer('fk_ptkp')->nullable();
            $table->string('npwp')->nullable();
            $table->string('gajipokok')->default(0);
            $table->string('address')->nullable();
            $table->integer('fk_bank')->nullable();
            $table->string('accountnumber')->nullable();
            $table->boolean('is_pemeriksapasien')->default(false);
            $table->timestamps();
        });

        $data = [
            [
                'nik' => 123213131,
                'fullname' => "Superadmin",
                'fk_designation' => 1,
                'is_pemeriksapasien' => true
            ],
            [
                'nik' => 123255132,
                'fullname' => "User1",
                'fk_designation' => 2,
                'is_pemeriksapasien' => true
            ],
            [
                'nik' => 122113133,
                'fullname' => "User2",
                'fk_designation' => 3,
                'is_pemeriksapasien' => true
            ],

        ];
        Employee::insert($data);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_employee');
    }
};
