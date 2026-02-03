<?php

namespace App\Utilities;

use App\Models\Currency;

class CurrencyConverter
{
    public static function convert($amount, $from = 'USD', $to = 'USD')
    {
        // dd($amount);
        $amount = trim(str_replace(',', '', (string) $amount));
        // $amount2 = self::formatAmount($amount);

        if ($from === $to) {
            return self::formatAmount($amount);
        }

        $rate = Currency::getRate($from, $to);
        $convertedAmount = bcmul($amount, $rate, 4); // multiply with 4 decimal places
        return self::formatAmount($convertedAmount);
    }

    public static function formatAmount($amount)
    {

        $amount = str_replace(',', '', $amount);  // remove commas

        $rounded = bcadd($amount, '0', 2);
        $parts = explode('.', $rounded);
        return number_format($parts[0], 0, '.', ',') . '.' . $parts[1];
    }


    public static function getSymbol($currency)
    {
        return Currency::getSymbol($currency);
    }
}
