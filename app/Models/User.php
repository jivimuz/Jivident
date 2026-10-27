<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = "m_users";
    protected $fillable = [
        'klienfk',
        'username',
        'email',
        'password',
        'force_pass_reset',
        'is_active',
        'access',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string, bool>
     */
    protected $hidden = [
        'password',
        'access',
        'remember_token'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean'
        ];
    }

    public function employee()
    {
        return $this->hasOne(Employee::class, 'id', 'fk_employee');
    }
}
