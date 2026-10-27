<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JadwalKerjaKaryawan extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $table = 't_jadwalkerjakaryawan';

    protected $guarded = ['id'];

    protected $casts = [
        'tanggal' => 'date',
        'jam_masuk' => 'datetime',
        'jam_pulang' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'fk_employee');
    }

    public function template()
    {
        return $this->belongsTo(JadwalKerja::class, 'fk_jadwalkerja');
    }
}
