<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RuanganOperasi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'm_ruangan_operasi';

    protected $guarded = ['id'];

    public function jadwal()
    {
        return $this->hasMany(JadwalOperasi::class, 'fk_ruangan_operasi');
    }
}
