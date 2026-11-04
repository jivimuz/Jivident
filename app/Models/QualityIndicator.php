<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class QualityIndicator extends Model
{
    use LogsActivity;

    protected $table = 't_quality_indicators';

    protected $guarded = ['id'];

    protected $casts = [
        'period_start' => 'date:Y-m-d',
        'period_end' => 'date:Y-m-d',
        'target' => 'decimal:4',
        'value' => 'decimal:4',
    ];
}
