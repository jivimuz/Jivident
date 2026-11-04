<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use LogsActivity, SoftDeletes;

    protected $table = 't_purchase_orders';

    protected $guarded = ['id'];

    protected $casts = ['po_date' => 'date:Y-m-d'];
}
