<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class OperasiRecord extends Model
{
    use LogsActivity;

    protected $table = 't_operasi_records';

    protected $guarded = ['id'];

    protected $casts = [
        'preop_checklist' => 'array',
        'consumables' => 'array',
        'implants' => 'array',
    ];

    public function jadwal()
    {
        return $this->belongsTo(JadwalOperasi::class, 'jadwal_operasi_id');
    }

    public function monitoring()
    {
        return $this->hasMany(OperasiMonitoring::class, 'operasi_record_id');
    }
}
