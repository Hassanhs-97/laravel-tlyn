<?php

namespace App\Services\Fee;

class FeeService
{
    const MIN_FEE = 500000;
    const MAX_FEE = 50000000;

    public static function calculateFee(int|float $amount, int|float $price): int
    {
        $totalPrice = $amount * $price;
        $percent    = self::getFeePercent($amount);
        $fee        = ($totalPrice * $percent) / 100;

        return (int) min(max($fee, self::MIN_FEE), self::MAX_FEE);
    }

    private static function getFeePercent(float $amount): float
    {
        if ($amount <= 1) {
            return 2.0;
        } elseif ($amount <= 10) {
            return 1.5;
        } else {
            return 1.0;
        }
    }
}
