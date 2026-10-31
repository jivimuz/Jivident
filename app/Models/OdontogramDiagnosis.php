<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OdontogramDiagnosis extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'm_odontogram_diagnosis';

    protected $guarded = ['id'];

    public $timestamps = true;

    protected $casts = [
        'is_reset' => 'boolean',
    ];
}
