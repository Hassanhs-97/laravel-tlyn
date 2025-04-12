<?php

if (!function_exists('rial_to_toman')) {
    function rial_to_toman($amount, $formatted = true)
    {
        $toman = (int)($amount / 10);

        return $formatted ? number_format($toman) : $toman;
    }
}
