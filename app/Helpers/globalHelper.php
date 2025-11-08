<?php

use Carbon\Carbon;

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
}
