<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollSlip extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 't_payroll_slips';

    protected $guarded = ['id'];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'fk_employee');
    }
}
