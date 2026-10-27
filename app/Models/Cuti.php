<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuti extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 't_cuti';

    protected $guarded = ['id'];

    protected $casts = [
        'tglpermohonan' => 'date',
        'startdate' => 'date',
        'enddate' => 'date',
        'approved_at' => 'datetime',
        'issakit' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'fk_employee');
    }

    public function jenisCuti()
    {
        return $this->belongsTo(JenisCuti::class, 'fk_jeniscuti');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
