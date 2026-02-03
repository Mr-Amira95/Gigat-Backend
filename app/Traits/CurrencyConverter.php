<?php

namespace App\Traits;

trait CurrencyConverter
{
    public function convertPrice($price)
    {
        $currency = app('currency');
        return round($price * $currency->exchange_rate, 2);
    }

    public function currencyCode()
    {
        return app('currency')->code;
    }
}
