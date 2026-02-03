<?php

namespace App\Http\Controllers\Api;

use App\Services\TagService;
use Illuminate\Http\Request;
use App\Services\ServiceService;
use App\Http\Resources\TagResource;
use App\Http\Controllers\Controller;
use App\Http\Resources\PlanResource;
use App\Http\Resources\ReviewResource;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\ServiceDetailsResource;
use App\Http\Resources\FeaturedServiceResource;
use App\Http\Requests\Api\GetServicesBySubCategoryRequest;
use App\Http\Requests\Api\ServiceRequest;
use App\Http\Requests\Api\UpdateServiceRequest;
use App\Http\Resources\PortfolioResource;
use App\Http\Resources\SubCategoryResource;
use App\Services\PortfolioService;
use App\Services\ReviewService;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Utilities\CurrencyConverter;
use App\Models\Currency;
use App\Models\Service;
use App\Models\SubCategory;
use App\Utilities\GoogleTranslator;
use App\Models\ServiceMedia;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    protected $serviceService;
    protected $tagService;
    protected $portfolioService;
    protected $reviewService;
    public function __construct(ServiceService $serviceService, TagService $tagService, PortfolioService $portfolioService, ReviewService $reviewService)
    {
        $this->serviceService = $serviceService;
        $this->tagService = $tagService;
        $this->portfolioService = $portfolioService;
        $this->reviewService = $reviewService;
    }
    public function getBySubCategory(GetServicesBySubCategoryRequest $request)
    {
        $subCategoryId = $request->query('sub_category_id');
        $perPage = $request->query('per_page', 15);

        $services = $subCategoryId
            ? $this->serviceService->getBySubCategory($subCategoryId, $perPage)
            : $this->serviceService->getAllActive($perPage);

        $tags = $this->tagService->getTagsBySubcategoryId($subCategoryId);

        return $this->successResponse(__('success'), [
            'services' => ServiceResource::collection($services['data']),
            'meta' => $services['meta'],
            'tags' => TagResource::collection($tags)
        ]);
    }
    public function getServicesByTag(Request $request, $tag)
    {
        $perPage = $request->query('per_page', 15);
        $services = $this->serviceService->getServicesByTag($tag, $perPage);
        return $this->successResponse(__('success'), [
            'services' => ServiceResource::collection($services['data']),
            'meta' => $services['meta']
        ]);
    }
    public function getRecommendedServices(Request $request)
    {
        $perPage = $request->query('per_page', 15);
        $services = $this->serviceService->getRecommendedServices($perPage);
        return $this->successResponse(__('success'), [
            'services' => ServiceResource::collection($services['data']),
            'meta' => $services['meta']
        ]);
    }
    public function getFeaturedServices(Request $request)
    {
        $perPage = $request->query('per_page', 15);
        $services = $this->serviceService->getFeaturedServices($perPage);
        return $this->successResponse(
            __('success'),
            [
                'services' => FeaturedServiceResource::collection($services['data']),
                'meta' => $services['meta']
            ]
        );
    }
    public function getServicesByUserId(Request $request)
    {
        $perPage = $request->query('per_page', 15);
        $userId =  Auth::id() ?? $request->query('user_id');
        // dd($userId);
        $services = $this->serviceService->getServicesByUserId($userId, $perPage);
        return $this->successResponse(
            __('success'),
            [
                'services' => ServiceResource::collection($services['data']),
                'meta' => $services['meta']
            ]
        );
    }
    public function serviceDetails(Request $request, $serviceId)
    {

        $currencyCode = $request->header('currency', 'USD');
        $currencyModel = Currency::where('code', strtoupper($currencyCode))->first();
        $symbol = $currencyModel ? $currencyModel->symbol : '$';
        // dd($currencyCode);
        $service = $this->serviceService->getServiceDetails($serviceId);
        if (!$service) {
            return $this->errorResponse(__('service_unavailable'), 404);
        }
        $portfolio = $this->portfolioService->getPortfolioByService($service->id);
        $recommended = $this->serviceService->getRelatedServices($serviceId);
        // $plans = $this->serviceService->getPlansByServiceId($serviceId);
        $plans = $this->serviceService
            ->getPlansByServiceId($serviceId)
            ->sortBy(fn($item) => $item->plan->id)
            ->values();

        $plans = $plans->map(function ($planItem) use ($currencyCode, $symbol) {

            return [
                'id' => $planItem->plan->id,
                'title' => $planItem->plan->translation->title,
                'features' => collect($planItem->features)->map(function ($feature) use ($currencyCode, $symbol) {
                    $value = $feature->value;

                    // Convert if it's a price type
                    if ($feature->type === 'price') {

                        $convertedValue = CurrencyConverter::convert($value, 'USD', $currencyCode);
                        $value = $convertedValue;
                    }

                    if ($feature->type === 'source_files') {
                        $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                    }

                    return [
                        'id' => $feature->id,
                        'key' => $feature->type,
                        'title' => $feature->translation->title,
                        'value' => $value,
                    ];
                }),
            ];
        });

        $avgUserRate = $this->reviewService->getAverageRatingByUser($service->user_id);
        return $this->successResponse(__('success'), [
            'service' => new ServiceDetailsResource($service, $avgUserRate),
            'portoflio' => PortfolioResource::collection($portfolio),
            'reviews' => ReviewResource::collection($service->reviews->load('user.profession')),
            'recommended' => ServiceResource::collection($recommended),
            'plans' => $plans,
        ]);
    }

    public function search(Request $request)
    {
        $query = trim($request->query('query', ''));
        $perPage = $request->query('per_page', 15);
        $subCategoryId = $request->query('sub_category_id');
        if (empty($query) || strlen($query) < 3) {
            return $this->successResponse(__('Please enter at least 2 characters to search'), [
                'services' => [],
                'meta' => [
                    'total' => 0,
                    'per_page' => $perPage,
                    'current_page' => 1
                ]
            ], 422);
        }
        $services = $this->serviceService->search($query, $subCategoryId, $perPage);
        return $this->successResponse(__('success'), [
            'services' => ServiceResource::collection($services['data']),
            'meta' => $services['meta']
        ]);
    }

    public function create(ServiceRequest $request)
    {
        try {

            $user = auth()->user();

            if (!$user->bank) {
                return $this->errorResponse(
                    __('account_inactive_complete_bank'),
                    403
                );
            }

            $currencyCode = $request->header('currency', 'USD');
            $currencyModel = Currency::where('code', strtoupper($currencyCode))->first();
            $symbol = $currencyModel ? $currencyModel->symbol : '$';

            $data = $request->validated();

            // Get currency code from header (default 'USD')
            $currencyCode = $request->currency;
            // dd($data['plans']);

            // Loop on each plan and convert price to USD
            foreach ($data['plans'] as &$plan) {
                foreach ($plan['features'] as &$feature) {
                    if ($feature['type'] === 'price') {
                        // Convert price to USD
                        $dd = $feature['value'] = CurrencyConverter::convert($feature['value'], $currencyCode, 'USD');
                    }
                }
            }
            // dd($dd);
            // Add user id
            $data['user_id'] = Auth::id();

            // Continue with saving
            $service = $this->serviceService->create($data);

            return $this->successResponse(__('success'), new ServiceDetailsResource($service));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    public function update(UpdateServiceRequest $request, $id)
    {

        try {
            $currencyCode = $request->currency ?? 'USD';

            $data = $request->validated();

            $data['user_id'] = Auth::id();

            foreach ($data['plans'] as &$plan) {
                foreach ($plan['features'] as &$feature) {
                    if ($feature['type'] === 'price') {
                        $feature['value'] = CurrencyConverter::convert($feature['value'], $currencyCode, 'USD');
                    }
                }
            }

            $updatedService = $this->serviceService->update($data, $id);

            return $this->successResponse(__('success'), new ServiceDetailsResource($updatedService));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    public function delete($id)
    {
        try {
            $this->serviceService->delete($id);
            return $this->successResponse(__('success'));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
    public function deleteMedia($id)
    {
        try {
            $userId = Auth::id();

            // Find the media that belongs to one of the freelancer's services
            $media = ServiceMedia::whereHas('service', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->find($id);

            if (!$media) {
                return response()->json(['message' => 'Unauthorized access'], 403);
            }

            // Delete media file if needed
            if ($media->path && Storage::exists($media->path)) {
                $this->serviceService->deleteMedia($id);
                return $this->successResponse(__('success'));
            }

            $media->delete();

            return $this->successResponse(__('success'));
        } catch (\Exception $e) {
            return $this->exceptionResponse($e);
        }
    }


    public function searchServicesAndSubCategories(Request $request)
    {
        $search = trim($request->query('search', ''));
        $perPage = $request->query('per_page', 15);

        $result = $this->serviceService->searchServicesAndSubCategories($search, $perPage);

        return $this->successResponse(__('success'), [
            'services' => ServiceResource::collection($result['services']->items()),
            'sub_categories' => SubCategoryResource::collection($result['sub_categories']),
        ]);
    }
    public function toggleActivation($id)
    {
        $data = $this->serviceService->toggleActivation($id);

        return $this->successResponse(
            __('success'),
            $data
        );
    }
}
