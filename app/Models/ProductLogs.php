<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductLogs extends Model
{
    use HasFactory;
    protected $table = 't_productlogs';
    protected $guarded = ["id"];
    public $timestamps = true;
}
