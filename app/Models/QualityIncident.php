<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QualityIncident extends Model
{
    use LogsActivity, SoftDeletes;

    protected $table = 't_quality_incidents';

    protected $guarded = ['id'];

    protected $casts = ['occurred_at' => 'datetime'];
}
