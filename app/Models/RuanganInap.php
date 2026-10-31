<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RuanganInap extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'm_ruangan_inap';

    protected $guarded = ['id'];

    public function beds()
    {
        return $this->hasMany(Kamar::class, 'fk_ruangan')->orderBy('nomor_ranjang');
    }
}
