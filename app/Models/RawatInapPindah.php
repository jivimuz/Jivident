<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawatInapPindah extends Model
{
    use HasFactory;

    protected $table = 't_rawatinap_pindah';

    protected $guarded = ['id'];

    protected $casts = [
        'waktu_pindah' => 'datetime',
    ];

    public function rawatInap()
    {
        return $this->belongsTo(RawatInap::class, 'fk_rawatinap');
    }

    public function kamarAsal()
    {
        return $this->belongsTo(Kamar::class, 'fk_kamar_asal');
    }

    public function kamarTujuan()
    {
        return $this->belongsTo(Kamar::class, 'fk_kamar_tujuan');
    }
}
