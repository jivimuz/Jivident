<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $table = 't_appointments';

    protected $guarded = ['id'];

    protected $casts = [
        // 'date:Y-m-d' (not plain 'date') so the stored/compared value is a bare
        // date string — plain 'date' round-trips through a full datetime format,
        // which breaks exact-string `where('tanggal', $dateString)` lookups used
        // for double-booking checks.
        'tanggal' => 'date:Y-m-d',
        'cancelled_at' => 'datetime',
    ];

    public function dokter()
    {
        return $this->belongsTo(User::class, 'fk_dokter');
    }

    public function ruangPraktek()
    {
        return $this->belongsTo(RuangPraktek::class, 'fk_ruangpraktek');
    }

    public function pasien()
    {
        return $this->belongsTo(Patient::class, 'fk_pasien');
    }
}
