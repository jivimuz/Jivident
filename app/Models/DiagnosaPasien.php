<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiagnosaPasien extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 't_diagnosapasien';
    protected $guarded = ["id"];
    public $timestamps = true;
}
