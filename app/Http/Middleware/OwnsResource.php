<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Service;
use App\Models\Portfolio;
use App\Models\Request as ServiceRequest;
use App\Models\Ticket;
use App\Models\FreelancerCertificate;

class OwnsResource
{
    public function handle(Request $request, Closure $next, $type)
    {
        $userId = Auth::id();
        $id = $request->route('id') ?? $request->route('ticket');

        $unauthorized = false;

        switch ($type) {
            case 'service':
                $record = Service::find($id);
                $unauthorized = !$record || $record->user_id !== $userId;
                break;

            case 'portfolio':
                $record = Portfolio::find($id);
                $unauthorized = !$record || $record->user_id !== $userId;
                break;

            case 'ticket':
                $record = Ticket::find($id);
                $unauthorized = !$record || $record->user_id !== $userId;
                break;

            case 'request':
                $record = ServiceRequest::with('service')->find($id);
                $unauthorized = !$record || !$record->service || $record->service->user_id !== $userId;
                break;

            case 'certificate':
                $record = FreelancerCertificate::find($id);
                $unauthorized = !$record || $record->user_id !== $userId;
                break;
        }

        if ($unauthorized) {
            // If API request → return JSON
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => __('unauthorized')], 403);
            }

            // If web request → redirect or show 403 page
            abort(403, __('unauthorized'));
            // or return redirect()->route('home')->withErrors(__('unauthorized'));
        }

        return $next($request);
    }
}
