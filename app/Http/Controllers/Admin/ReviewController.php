<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserRating;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = UserRating::with('rater', 'rated')->latest()->get();
        return view('pages.reviews.index', compact('reviews'));
    }
}
