<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Klien extends Model
{

    use HasFactory;
    protected $table = 'm_klien';
    protected $guarded = ["id"];
    public $timestamps = false;
}
