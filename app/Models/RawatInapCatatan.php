<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawatInapCatatan extends Model
{
    use HasFactory;

    protected $table = 't_rawatinap_catatan';

    protected $guarded = ['id'];

    protected $casts = [
        'waktu' => 'datetime',
    ];
}
