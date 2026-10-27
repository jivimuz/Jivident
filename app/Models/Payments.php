<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    use HasFactory, LogsActivity;
    protected $table = 'm_payments';
    protected $guarded = ["id"];
    public $timestamps = false;
}
