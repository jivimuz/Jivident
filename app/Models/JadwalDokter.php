<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JadwalDokter extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $table = 't_jadwaldokter';

    protected $guarded = ['id'];

    public function dokter()
    {
        return $this->belongsTo(User::class, 'fk_dokter');
    }

    public function ruangPraktek()
    {
        return $this->belongsTo(RuangPraktek::class, 'fk_ruangpraktek');
    }
}
