<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * Semua route di bawah `pemeriksaan/pasien/{id}` (termasuk Odontogram) pakai
 * `{id}` yang sama — base64-double-encoded dari `t_daftarpasien.id` — tapi
 * hampir semua method di PemeriksaanController cuma `base64_decode` lalu
 * langsung query tanpa pernah cek apakah kunjungan itu milik klienfk user
 * yang login. Middleware ini jadi satu titik pemeriksaan untuk SELURUH grup
 * route itu sekaligus, daripada menambah `where('klienfk', ...)` manual di
 * setiap method satu-satu (dan berisiko ada yang terlewat seperti yang sudah
 * terjadi di Lab/Radiologi/Consent).
 *
 * Query lewat DB::table (bukan model DaftarPasien) supaya tidak kena global
 * scope "exclude Dibatalkan" — status kunjungan bukan urusan middleware ini,
 * cuma soal kepemilikan cabang.
 */
class VerifyDaftarPasienBranch
{
    public function handle(Request $request, Closure $next): Response
    {
        $encodedId = $request->route('id');
        $daftarpasienId = base64_decode(base64_decode((string) $encodedId));

        $belongsToKlien = DB::table('t_daftarpasien')
            ->where('id', $daftarpasienId)
            ->where('klienfk', Auth::user()->klienfk ?? 1)
            ->exists();

        if (! $belongsToKlien) {
            abort(404);
        }

        return $next($request);
    }
}
