<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    protected $table = "m_kecamatan";
    protected $guarded = ["id"];
    public $timestamps = false;
}
