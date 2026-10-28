<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InformedConsent extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $table = 't_informed_consent';

    protected $guarded = ['id'];
}
