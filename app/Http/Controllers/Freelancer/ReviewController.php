<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Services\ReviewService;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    protected $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    public function index(){
        $reviews = $this->reviewService->getForFreelancer(auth()->id());
        return view('pages-freelancer.reviews.index',compact('reviews'));
    }
}
