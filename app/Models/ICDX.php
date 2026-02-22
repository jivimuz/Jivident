<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ICDX extends Model
{
    use HasFactory;
    protected $table = 'm_icdx';
    protected $guarded = ["id"];
    public $timestamps = true;
}
