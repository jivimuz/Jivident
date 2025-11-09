<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RuangPraktek extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'm_ruangpraktek';
    protected $guarded = ["id"];
    public $timestamps = true;
}
