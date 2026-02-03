<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetDashboardLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        
        $locale = session('locale', config('app.locale'));

        
        if (!in_array($locale, ['en', 'ar'])) {
            $locale = config('app.locale');
        }

        
        App::setLocale($locale);

        
        session()->put('is_rtl', $locale === 'ar');

        return $next($request);
    }
}
