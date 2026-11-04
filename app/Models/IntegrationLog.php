<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class IntegrationLog extends Model
{
    use LogsActivity;

    protected $table = 't_integration_logs';

    protected $guarded = ['id'];

    protected $casts = [
        'request_payload' => 'array',
        'response_payload' => 'array',
        'sent_at' => 'datetime',
    ];
}
