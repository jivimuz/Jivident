<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperasiMonitoring extends Model
{
    protected $table = 't_operasi_monitoring';

    protected $guarded = ['id'];

    protected $casts = ['observed_at' => 'datetime'];

    public function record()
    {
        return $this->belongsTo(OperasiRecord::class, 'operasi_record_id');
    }
}
