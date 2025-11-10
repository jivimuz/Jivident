<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VitalSign extends Model
{

    use HasFactory, SoftDeletes;
    protected $table = 't_vitalsign';
    protected $guarded = ["id"];
    public $timestamps = true;
}
