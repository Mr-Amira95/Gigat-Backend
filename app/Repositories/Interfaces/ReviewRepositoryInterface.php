<?php

namespace App\Repositories\Interfaces;

interface ReviewRepositoryInterface
{
    public function index();
    public function submitReview(array $data);
    public function getReviewsByUser($userId, $perPage = null);
    public function getReviewsByService($serviceId, $perPage = null);
    public function getUserServiceReview($userId, $serviceId);
    public function getReviewsCount($serviceId);
    public function getAverageRatingByUser($userId);
} 