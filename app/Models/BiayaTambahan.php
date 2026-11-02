<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BiayaTambahan extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $table = 't_biaya_tambahan';

    protected $guarded = ['id'];

    public function daftarPasien()
    {
        return $this->belongsTo(DaftarPasien::class, 'daftarpasien_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
