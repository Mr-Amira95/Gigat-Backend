<?php

namespace App\Repositories\Eloquents;

use App\Models\Review;
use App\Models\Service;
use Illuminate\Support\Facades\Log;
use App\Repositories\Interfaces\ReviewRepositoryInterface;
use App\Traits\PaginateTrait;
use App\Utilities\GoogleTranslator;

class ReviewRepository implements ReviewRepositoryInterface
{
    use PaginateTrait;
    protected $model;
    protected $service;
    protected $googleTranslator;
 
    public function __construct(Review $model, Service $service, GoogleTranslator $googleTranslator)
    {
        $this->model = $model;
        $this->service = $service;
        $this->googleTranslator = $googleTranslator;
    }
    public function index()
    {
        return $this->model->with('user', 'service')->latest()->paginate(9);
    }


    // public function getForFreelancer($userId)
    // {
    //     // $userId = auth()->id();
    //     // dd($userId);
    //     return $this->model
    //         ->whereHas('service', function ($query) use ($userId) {
    //             $query->where('user_id', $userId);
    //         })
    //         ->with(['user', 'service.user'])  // note: 'services' plural
    //         ->orderBy('id', 'DESC')
    //         ->get();
    // }
    public function getForFreelancer($userId)
    {
        return $this->model
            ->whereHas('service', function ($query) use ($userId) {
                $query->withTrashed()->where('user_id', $userId);
            })
            ->with([
                'user',
                'service' => function ($q) {
                    $q->withTrashed()->with('user');
                }
            ])
            ->orderBy('id', 'DESC')
            ->get();
    }

    public function getByUserAndService(int $userId, int $serviceId)
    {
        return $this->model->where('user_id', $userId)
            ->where('service_id', $serviceId)
            ->first();
    }
    public function getServiceAverageRating(int $serviceId)
    {
        return $this->model->where('service_id', $serviceId)->avg('rating');
    }
    public function submitReview(array $data)
    {
        // $review = $this->model->create($data);
        $review = $this->model->create([
            'service_id' => $data['service_id'],
            'user_id'    => $data['user_id'],
            'rating'     => $data['rating'],
            'status'     => $data['status'] ?? 'approved',
        ]);

        // 2. Translate comment
        if (!empty($data['comment'])) {
            $translations = $this->googleTranslator->translateForStorage($data['comment']);

            foreach ($translations as $lang => $text) {
                $review->translations()->create([
                    'language' => $lang,
                    'comment'  => $text,
                ]);
            }
        }

        $review->load(['user' => function ($query) {
            $query->with(['languages.language']);
        }]);
        $averageRating = $this->getServiceAverageRating($data['service_id']);
        $count = $this->getReviewsCount($data['service_id']);
        $this->service->withTrashed()->find($data['service_id'])->update(['rating' => $averageRating, 'reviews_count' => $count]);
        return $review;
    }
    public function getReviewsByUser($userId, $perPage = null)
    {
        $query = $this->model->where('user_id', $userId)
            ->with(['service', 'user'])
            ->latest();
        return $this->paginate($query, $perPage);
    }
    public function getReviewsByService($serviceId, $perPage = null)
    {
        $query = $this->model->where('service_id', $serviceId)
            ->with(['service', 'user'])
            ->latest();
        return $this->paginate($query, $perPage);
    }
    public function getUserServiceReview($userId, $serviceId)
    {
        return $this->model->where('user_id', $userId)
            ->where('service_id', $serviceId)
            ->first();
    }
    public function getReviewsCount($serviceId)
    {
        return $this->model->where('service_id', $serviceId)->count();
    }
    // public function getAverageRatingByUser($userId)
    // {
    //     return number_format((float)$this->model->where('user_id', $userId)->avg('rating'));
    // }
    public function getAverageRatingByUser($userId)
    {
        $avg = $this->model
            ->whereHas('service', fn($q) => $q->withTrashed()->where('user_id', $userId))
            ->avg('rating');

            return $avg ? number_format(round((float)$avg, 1), 1) : 0.0;
    }
}
