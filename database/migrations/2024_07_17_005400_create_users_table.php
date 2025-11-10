<?php

use App\Models\Module;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('m_users', function (Blueprint $table) {
            $table->id();
            $table->string('klienfk', 30)->default(1);
            $table->integer('fk_employee')->unique();
            $table->string('username')->unique();
            $table->string('display_name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('force_pass_reset')->default(true);
            $table->string('access')->default('1');
            $table->boolean('is_active')->default(false);
            $table->boolean('is_resetpass')->default(false);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('jwt_token', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->longText('token');
            $table->timestamp('create_at');
            $table->timestamp('expire_at');
            $table->boolean('is_active')->default(true);

            $table->foreign('user_id')->references('id')->on('m_users');
        });


        $a = Module::select('id')->orderBy('id', 'asc')->get();
        $b = [];
        foreach ($a as $i) {
            $b[] = $i->id;
        }
        $b = implode(',', $b);

        $data = [
            [
                'fk_employee' => 1,
                'username' => "superadmin",
                'display_name' => "Super Admin",
                'email' => "test@test.com",
                'email_verified_at' => now(),
                'password' =>  Hash::make('12341234'),
                'remember_token' => Str::random(10),
                'is_active' => true,
                'access' => $b
            ],
            [
                'fk_employee' => 2,
                'username' => "user1",
                'display_name' => "User 1",
                'email' => "user1@test.com",
                'email_verified_at' => now(),
                'password' =>  Hash::make('12341234'),
                'remember_token' => Str::random(10),
                'is_active' => true,
                'access' => 1
            ],
            [
                'fk_employee' => 3,
                'username' => "user2",
                'display_name' => "User 2",
                'email' => "user2@test.com",
                'email_verified_at' => now(),
                'password' =>  Hash::make('12341234'),
                'remember_token' => Str::random(10),
                'is_active' => true,
                'access' => 1
            ],



        ];
        User::insert($data);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jwt_token');
        Schema::dropIfExists('m_users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
