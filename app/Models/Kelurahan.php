<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelurahan extends Model
{

    protected $table = "m_kelurahan";
    protected $guarded = ["id"];
    public $timestamps = false;
}
