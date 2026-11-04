<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProcurementRequest extends Model
{
    use LogsActivity, SoftDeletes;

    protected $table = 't_procurement_requests';

    protected $guarded = ['id'];

    protected $casts = ['approved_at' => 'datetime'];

    public function items()
    {
        return $this->hasMany(ProcurementRequestItem::class);
    }
}
