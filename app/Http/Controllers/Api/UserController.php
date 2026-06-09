<?php

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use App\Services\ClientService;
use App\Services\ReviewService;
use App\Services\ServiceService;
use App\Services\PortfolioService;
use App\Services\FreelancerService;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\ReviewResource;
use App\Http\Resources\UserRatingResource;
use App\Http\Resources\ServiceResource;
use App\Models\UserRating;
use App\Http\Resources\PortfolioResource;
use App\Http\Resources\FreelancerResource;
use App\Http\Requests\Api\UpdateProfileRequest;
use App\Http\Requests\Api\BecomeFreelancerRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Freelancer;
use App\Models\Notification;
use App\Models\PlayerId;
use App\Models\User;
use App\Services\CategoryService;
use App\Services\NoticeService;
use App\Services\OneSignalService;

class UserController extends Controller
{
    protected $freelancerService;
    protected $clientService;
    protected $reviewService;
    protected $portfolioService;
    protected $serviceService;
    protected $categoryService;
    protected $noticeService;

    public function __construct(FreelancerService $freelancerService, ClientService $clientService, ReviewService $reviewService, PortfolioService $portfolioService, ServiceService $serviceService, CategoryService $categoryService, NoticeService $noticeService)
    {
        $this->freelancerService = $freelancerService;
        $this->clientService = $clientService;
        $this->reviewService = $reviewService;
        $this->portfolioService = $portfolioService;
        $this->categoryService = $categoryService;
        $this->serviceService = $serviceService;
        $this->noticeService    = $noticeService;
    }
    public function getClientProfile()
    {
        try {
            $userId = Auth::guard('api')->id();
            if (!$userId) {
                return $this->errorResponse(__('unauthorized'));
            }

            $user = $this->clientService->getUserProfile($userId);
            return $this->successResponse(__('user_profile_retrieved'), new UserResource($user));
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    public function getFreelancerCategoriesByUserId()
    {
        try {
            $categories = $this->categoryService->getUserCategoriesApi();
            return $this->successResponse(__('user_categories_retrieved'), CategoryResource::collection($categories));
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    public function getFreelancerProfile()
    {
        try {
            $userId = Auth::guard('api')->id();
            if (!$userId) {
                return $this->errorResponse(__('unauthorized'));
            }
            $user = $this->freelancerService->getUserProfile($userId);
            $ratings = UserRating::where('ratee_id', $userId)->with('rater')->latest()->get();
            $averageRating = $ratings->avg('rating') ?? 0;
            $portfolio = $this->portfolioService->getPortfolioByUserId($userId);
            $services = $this->serviceService->getServicesByUserId($userId, $perPage = 10);

            return $this->successResponse(
                __('user_profile_retrieved'),
                [
                    'freelancer' => new FreelancerResource($user),
                    'average_rating' => round((float) $averageRating, 1),
                    'ratings' => [
                        'data' => UserRatingResource::collection($ratings),
                    ],
                    'portfolio' => [
                        'data' => PortfolioResource::collection($portfolio['data']),
                        'meta' => $portfolio['meta']
                    ],
                    'services' => [
                        'data' => ServiceResource::collection($services['data']),
                        'meta' => $services['meta']
                    ]
                ]
            );
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
    public function getFreelancerProfileByUserId(Request $request, $userId)
    {
        try {
            $perPage = $request->query('per_page', 15);
            $user = $this->freelancerService->getUserProfile($userId);
            if (!$user) {
                return $this->errorResponse(__('user_not_found'));
            }
            $ratings = UserRating::where('ratee_id', $userId)->with('rater')->latest()->get();
            $averageRating = $ratings->avg('rating') ?? 0;
            $portfolio = $this->portfolioService->getPortfolioByUserId($userId);
            $services = $this->serviceService->getServicesByUserId($userId, $perPage);

            return $this->successResponse(
                __('user_profile_retrieved'),
                [
                    'freelancer' => new FreelancerResource($user),
                    'average_rating' => round((float) $averageRating, 1),
                    'ratings' => [
                        'data' => UserRatingResource::collection($ratings),
                    ],
                    'portfolio' => [
                        'data' => PortfolioResource::collection($portfolio['data']),
                        'meta' => $portfolio['meta']
                    ],
                    'services' => [
                        'data' => ServiceResource::collection($services['data']),
                        'meta' => $services['meta']
                    ]
                ]
            );
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
    public function completeProfile(BecomeFreelancerRequest $request)
    {
        try {
            $freelancer = $this->freelancerService->completeProfile($request->validated());

            // $user = $freelancer;
            // // one signal notification*****************************************
            // if ($user) {
            //     $titles = [
            //         'en' => __('messages.welcome_freelancer_title', [], 'en'),
            //         'ar' => __('messages.welcome_freelancer_title', [], 'ar'),
            //     ];

            //     $messages = [
            //         'en' => __('messages.welcome_freelancer_message', [], 'en'),
            //         'ar' => __('messages.welcome_freelancer_message', [], 'ar'),
            //     ];

            //     $this->noticeService->send(
            //         $user->id,
            //         $titles,
            //         $messages,
            //         'new_freelancer',
            //         null,
            //         true
            //     );
            // }
            // *********************************************//
            return $this->successResponse(__('freelancer_created'), new UserResource($freelancer));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
    public function updateProfile(UpdateProfileRequest $request)
    {
        try {
            $userId = Auth::guard('api')->id();
            if (!$userId) {
                return $this->errorResponse(__('unauthorized'));
            }
            $data = $request->validated();

            // Load user with freelancer relation
            $user = User::with('freelancer')->findOrFail($userId);

            if ($user->freelancer) {
                // User is a freelancer
                $updatedUser = $this->freelancerService->updateProfile($userId, $data);
            } else {
                // User is a client
                $updatedUser = $this->clientService->updateProfile($userId, $data);
            }

            if (!$updatedUser) {
                return $this->errorResponse(__('update_failed'));
            }
            return $this->successResponse(
                __('profile_updated_successfully'),
                new UserResource($updatedUser)
            );
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
    public function changePassword(Request $request)
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user) {
                return $this->errorResponse(__('unauthorized'), 401);
            }

            $data = $request->validate([
                'old_password' => 'required',
                'new_password' => 'required|confirmed|min:8',
            ]);

            if (!Hash::check($data['old_password'], $user->password)) {
                return $this->errorResponse(__('The old password is incorrect.'), 422);
            }

            $user->update([
                'password' => Hash::make($data['new_password']),
            ]);

            return $this->successResponse(__('Password changed successfully.'));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    public function uploadFileVerification(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf,doc,docx,jpg,jpeg,png,gif,webp|max:2048'
        ]);

        $user = auth()->user();

        // get or create freelancer record for this user
        $freelancer = Freelancer::where('user_id', $user->id)->first();

        // upload file
        if ($request->hasFile('file')) {
            $file = $request->file;
            $filePath = $this->uploadFiles($file, 'freelancer');

            $freelancer->file = $filePath;
        }
        $freelancer->save();


        return $this->successResponse(__('file_uploaded_successfully'));
    }
}
