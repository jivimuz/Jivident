<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RawatInapCarePlan extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $table = 't_rawatinap_careplans';

    protected $guarded = ['id'];

    protected $casts = [
        'target_date' => 'date:Y-m-d',
        'evaluated_at' => 'datetime',
    ];

    public function admission()
    {
        return $this->belongsTo(RawatInap::class, 'fk_rawatinap');
    }
}
