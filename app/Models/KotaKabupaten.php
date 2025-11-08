<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KotaKabupaten extends Model
{
    use HasFactory;

    protected $table = "m_kotakabupaten";
    protected $guarded = [];
    public $timestamps = false;
}
