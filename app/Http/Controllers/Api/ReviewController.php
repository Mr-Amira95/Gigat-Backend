<?php

namespace App\Http\Controllers\Api;

use App\Traits\BaseResponse;
use App\Services\ReviewService;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Http\Requests\Api\ReviewRequest;
use App\Models\Notification;
use App\Models\PlayerId;
use App\Models\Service;
use App\Services\NoticeService;
use App\Services\OneSignalService;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    use BaseResponse;

    protected $reviewService;
    protected $noticeService;


    public function __construct(ReviewService $reviewService, NoticeService $noticeService)
    {
        $this->reviewService = $reviewService;
        $this->noticeService    = $noticeService;
    }

    public function submitReview(ReviewRequest $request)
    {
        try {
            $data = $request->validated();

            $service = Service::withTrashed()->find($data['service_id']);

            if (!$service) {
                return $this->errorResponse(__('service_unavailable'), 404);
            }
            $review = $this->reviewService->submitReview($data);
            if (isset($review['error']) && $review['error']) {
                return $this->errorResponse($review['message']);
            }

            // $user = $review->service->user;
            $service = $review->service;
            $user = $service?->user;

            // one signal notification*****************************************
            if ($user) {
                $titles = [
                    'en' => __('messages.rated_title', [], 'en'),
                    'ar' => __('messages.rated_title', [], 'ar'),
                ];

                $messages = [
                    'en' => __('messages.rated_message', ['client' => $review->user->username], 'en'),
                    'ar' => __('messages.rated_message', ['client' => $review->user->username], 'ar'),
                ];

                $this->noticeService->send(
                    $user->id,
                    $titles,
                    $messages,
                    'rate',
                    $review->service_id
                );
            }
            // *********************************************//
            return $this->successResponse(
                __('review_submitted_successfully'),
                new ReviewResource($review)
            );
        } catch (\Exception $e) {
            return $this->exceptionResponse($e, __('failed_to_submit_review'));
        }
    }
    public function getReviewsByUser(Request $request, $userId)
    {
        $perPage = $request->query('per_page');
        try {
            $reviews = $this->reviewService->getReviewsByUser($userId, $perPage);
            return $this->successResponse(
                __('reviews_retrieved_successfully'),
                [
                    'reviews' => ReviewResource::collection($reviews['data']),
                    'meta' => $reviews['meta']

                ]
            );
        } catch (\Exception $e) {
            return $this->exceptionResponse($e, __('failed_to_retrieve_reviews'));
        }
    }
    public function getReviewsByService($serviceId)
    {
        try {
            $perPage = request()->query('per_page');
            $reviews = $this->reviewService->getReviewsByService($serviceId, $perPage);
            return $this->successResponse(
                __('reviews_retrieved_successfully'),
                [
                    'reviews' => ReviewResource::collection($reviews['data']),
                    'meta' => $reviews['meta']

                ]
            );
        } catch (\Exception $e) {
            return $this->exceptionResponse($e, __('failed_to_retrieve_reviews'));
        }
    }
}
