<?php

namespace App\Services;

use App\Repositories\Interfaces\ReviewRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class ReviewService
{
    protected $reviewRepository;

    public function __construct(ReviewRepositoryInterface $reviewRepository)
    {
        $this->reviewRepository = $reviewRepository;
    }

    public function index(){
        return $this->reviewRepository->index();
    }
    public function getForFreelancer($userId){
        return $this->reviewRepository->getForFreelancer($userId);
    }
    public function submitReview(array $data)
    {
        $existingReview = $this->reviewRepository->getUserServiceReview(
            Auth::id(),
            $data['service_id']
        );

        if ($existingReview) {
           return  [
            'error'=>true,
           'message'=> ('You have already reviewed this service')
        ];
        }
        $data['user_id'] = Auth::id();
        return $this->reviewRepository->submitReview($data);
    }
    public function getReviewsByUser($userId,$perPage = null)
    {
        return $this->reviewRepository->getReviewsByUser($userId,$perPage);
    }
    public function getReviewsByService($serviceId,$perPage = null)
    {
        return $this->reviewRepository->getReviewsByService($serviceId,$perPage);
    }
    public function getReviewsCount($serviceId)
    {
        return $this->reviewRepository->getReviewsCount($serviceId);
    }
    public function getAverageRatingByUser($userId)
    {
        return $this->reviewRepository->getAverageRatingByUser($userId);
    }
}
