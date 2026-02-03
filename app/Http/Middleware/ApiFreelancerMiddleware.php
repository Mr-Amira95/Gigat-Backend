<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiFreelancerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('api')->user();

        if (!$user || !$user->freelancer) {
            return response()->json([
                'message' => __('unauthorized')
            ], 403);
        }

        return $next($request);
    }
}
