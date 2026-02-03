<?php

namespace App\Http\Controllers\Admin;

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
        $reviews = $this->reviewService->index();
        return view('pages.reviews.index',compact('reviews'));
    }
}
