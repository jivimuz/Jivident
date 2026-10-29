<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JenisPemeriksaanLab extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'm_jenispemeriksaanlab';

    protected $guarded = ['id'];
}
