<?php

namespace App\Http\Middleware;

use App\Models\Currency;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CurrencyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $currencyCode = $request->header('currency', 'USD'); // default USD

        $currency = Currency::where('code', $currencyCode)->first();

        if ($currency) {
            app()->instance('currency', $currency);
        } else {
            $defaultCurrency = Currency::where('code', 'USD')->first();
            app()->instance('currency', $defaultCurrency);
        }

        return $next($request);
    }
}
