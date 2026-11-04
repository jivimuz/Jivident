<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BpjsClaim extends Model
{
    use LogsActivity, SoftDeletes;

    protected $table = 't_bpjs_claims';

    protected $guarded = ['id'];

    protected $casts = [
        'service_date' => 'date:Y-m-d',
        'submitted_at' => 'datetime',
        'verified_at' => 'datetime',
        'response_payload' => 'array',
        'amount' => 'decimal:2',
    ];

    public function visit()
    {
        return $this->belongsTo(DaftarPasien::class, 'daftarpasien_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'pasien_id');
    }
}
