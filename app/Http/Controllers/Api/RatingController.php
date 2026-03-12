<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserRating;
use App\Models\User;
use App\Models\Request as ServiceRequest;

class RatingController extends Controller
{
    public function rateClient(Request $request)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string',
            'request_id' => 'required|exists:requests,id'
        ]);

        $user = $request->user();

        $exists = UserRating::where('freelancer_id', $user->id)
            ->where('request_id', $request->request_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'You already rated this client'
            ], 422);
        }

        $serviceRequest = ServiceRequest::find($request->request_id);
        if (!$serviceRequest || $serviceRequest->service->user_id != $user->id) {
            return response()->json([
                'message' => 'Invalid request or client'
            ], 422);
        }
        
        $rating = UserRating::create([
            'freelancer_id' => $user->id,
            'client_id' => $serviceRequest->user_id,
            'request_id' => $request->request_id,
            'rating' => $request->rating,
            'review' => $request->review
        ]);

        $avg = UserRating::where('client_id', $serviceRequest->user_id)->avg('rating');
        User::where('id', $serviceRequest->user_id)->update([
            'rating' => $avg
        ]);

        return response()->json([
            'message' => 'Rating submitted successfully',
            'data' => $rating
        ]);
    }
}
