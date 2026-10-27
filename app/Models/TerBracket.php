<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TerBracket extends Model
{
    use HasFactory;

    protected $table = 'm_ter_brackets';

    protected $guarded = ['id'];
}
