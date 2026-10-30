<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SuratMandiri extends Model
{
    use LogsActivity, SoftDeletes;

    protected $table = 't_surat_mandiri';

    protected $guarded = ['id'];

    protected $casts = [
        'detail' => 'array',
        'harga' => 'float',
    ];

    public function dokter()
    {
        return $this->belongsTo(Employee::class, 'fk_employee');
    }

    public function payment()
    {
        return $this->belongsTo(Payments::class, 'fk_payment');
    }

    public function transaction()
    {
        return $this->belongsTo(Transactions::class, 'transaction_id');
    }
}
