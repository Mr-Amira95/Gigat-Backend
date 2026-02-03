<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class FreelancerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // dd($request);
        // لو مش مسجل دخول
        if (!Auth::guard('freelancer')->check()) {
            return redirect('/freelancer/login')->with('error', 'Please log in to access this page.');
        }

        // // لو مش Freelancer
        // if (!Auth::user()->is_freelancer) {
        //     return redirect('/')->with('error', 'You do not have permission to access this page.');
        // }

        return $next($request);
    }
}
