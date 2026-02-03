<?php

namespace App\Models;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = ['code', 'name', 'symbol_en','symbol_ar', 'exchange_rate'];
    public static function getRate($from, $to)
    {
        if ($from === $to) {
            return 1;
        }

        $fromCurrency = self::where('code', strtoupper($from))->first();
        $toCurrency = self::where('code', strtoupper($to))->first();

        if (!$fromCurrency || !$toCurrency) {
            return 1;
        }

        return $toCurrency->exchange_rate / $fromCurrency->exchange_rate;
    }

    public static function getSymbol($currency)
    {
        return self::where('code', strtoupper($currency))->value('symbol') ?? '$';
    }
    public function getSymbolAttribute()
    {
        $locale = App::getLocale();

        return $locale === 'ar' ? $this->symbol_ar : $this->symbol_en;
    }
}
