<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RawatInap extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $table = 't_rawatinap';

    protected $guarded = ['id'];

    protected $casts = [
        'tgl_masuk' => 'datetime',
        'tgl_keluar' => 'datetime',
    ];

    public function kamar()
    {
        return $this->belongsTo(Kamar::class, 'fk_kamar');
    }

    public function pasien()
    {
        return $this->belongsTo(Patient::class, 'fk_pasien');
    }

    public function catatan()
    {
        return $this->hasMany(RawatInapCatatan::class, 'fk_rawatinap');
    }
}
