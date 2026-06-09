<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\UserRating;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = UserRating::where('ratee_id', auth()->id())
            ->with('rater')
            ->latest()
            ->get();
        return view('pages-freelancer.reviews.index', compact('reviews'));
    }
}
