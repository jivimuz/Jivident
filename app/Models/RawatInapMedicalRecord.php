<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RawatInapMedicalRecord extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $table = 't_rawatinap_medical_records';

    protected $guarded = ['id'];

    protected $casts = [
        'follow_up_date' => 'date:Y-m-d',
    ];

    public function admission()
    {
        return $this->belongsTo(RawatInap::class, 'fk_rawatinap');
    }
}
