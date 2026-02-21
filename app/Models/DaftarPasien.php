<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class DaftarPasien extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 't_daftarpasien';
    protected $guarded = ["id"];
    public $timestamps = true;

    // Nama scope biar gampang di-remove
    public const SCOPE_NOT_CANCELED = 'not_canceled';

    protected static function booted(): void
    {
        // Default: semua query otomatis TIDAK menampilkan yang Dibatalkan
        static::addGlobalScope(self::SCOPE_NOT_CANCELED, function (Builder $builder) {
            $builder->where('status', '!=', 'Dibatalkan');
        });
    }

    /**
     * Tampilkan SEMUA data (termasuk yang Dibatalkan)
     * Mirip withTrashed()
     */
    public function scopeWithDibatalkan(Builder $query): Builder
    {
        return $query->withoutGlobalScope(self::SCOPE_NOT_CANCELED);
    }

    /**
     * Hanya data yang Dibatalkan
     * Mirip onlyTrashed()
     */
    public function scopeOnlyDibatalkan(Builder $query): Builder
    {
        return $query->withoutGlobalScope(self::SCOPE_NOT_CANCELED)
            ->where('status', 'Dibatalkan');
    }

    /**
     * Opsional: explicit untuk non-dibatalkan (kalau mau dipakai di tempat lain)
     */
    public function scopeWithoutDibatalkan(Builder $query): Builder
    {
        return $query->withoutGlobalScope(self::SCOPE_NOT_CANCELED)
            ->where('status', '!=', 'Dibatalkan');
    }
}
