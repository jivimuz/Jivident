<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

if (! function_exists('WIB')) {
    /**
     * Convert time to WIB
     *
     * @param string|\DateTime $time
     * @param string $format
     * @return string
     */
    function WIB($time, $format = 'Y-m-d H:i:s')
    {
        return Carbon::parse($time)->timezone('Asia/Jakarta')->format($format);
    }

    function generateNoRM($patientId)
    {
        // Ambil klienfk dari user yang login
        $klienfk = Auth::user()->klienfk; // misal 1, 23, 456

        // Ambil 3 digit terakhir dari klienfk, pad kiri dengan 0 jika kurang dari 3 digit
        $klienPart = str_pad(substr($klienfk, -3), 3, '0', STR_PAD_LEFT);

        // Ambil 4 digit dari patient ID, pad kiri dengan 0
        $idPart = str_pad($patientId, 4, '0', STR_PAD_LEFT);

        return "JD{$klienPart}-{$idPart}";
    }
}
