<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PosSale extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $table = 't_possales';

    protected $guarded = ['id'];

    public function items()
    {
        return $this->hasMany(PosSaleItem::class, 'possale_id');
    }

    public function payment()
    {
        return $this->belongsTo(Payments::class, 'fk_payment');
    }

    public function userhandler()
    {
        return $this->belongsTo(User::class, 'userhandler_id');
    }
}
