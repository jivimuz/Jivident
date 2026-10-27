<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TindakanPasien extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;
    protected $table = 't_tindakanpasien';
    protected $guarded = ["id"];
    public $timestamps = true;
}
