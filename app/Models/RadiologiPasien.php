<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RadiologiPasien extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $table = 't_radiologipasien';

    protected $guarded = ['id'];

    protected $casts = [
        'tgl_diminta' => 'datetime',
        'tgl_jadwal' => 'datetime',
        'tgl_hasil' => 'datetime',
    ];
}
