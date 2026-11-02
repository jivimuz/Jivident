<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JadwalOperasi extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $table = 't_jadwal_operasi';

    protected $guarded = ['id'];

    protected $casts = [
        // 'date:Y-m-d', bukan 'date' polos — lihat gotcha yang sama di
        // Appointment::$casts: cast 'date' round-trip ke datetime penuh,
        // yang bikin pengecekan bentrok ruangan/dokter (cocokkan string
        // tanggal polos) tidak pernah match.
        'tanggal' => 'date:Y-m-d',
        'cancelled_at' => 'datetime',
    ];

    public function ruangan()
    {
        return $this->belongsTo(RuanganOperasi::class, 'fk_ruangan_operasi');
    }

    public function dokter()
    {
        return $this->belongsTo(User::class, 'fk_dokter');
    }

    public function pasien()
    {
        return $this->belongsTo(Patient::class, 'fk_pasien');
    }

    public function daftarPasien()
    {
        return $this->belongsTo(DaftarPasien::class, 'fk_daftarpasien');
    }
}
