<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceRequest;
use App\Http\Requests\Admin\UpdateServiceRequest;
use App\Models\Currency;
use App\Models\Notification;
use App\Models\Plan;
use App\Models\PlayerId;
use App\Models\Service;
use App\Services\CategoryService;
use App\Services\FreelancerService;
use App\Services\NoticeService;
use App\Services\OneSignalService;
use App\Services\PlanService;
use App\Services\ServiceService;
use App\Services\TagService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    protected $serviceService;
    protected $categoryService;
    protected $freelancerService;
    protected $tagService;
    protected $planService;
    protected $noticeService;

    public function __construct(ServiceService $serviceService, CategoryService $categoryService, FreelancerService $freelancerService, TagService $tagService, PlanService $planService, NoticeService $noticeService)
    {
        $this->serviceService = $serviceService;
        $this->categoryService = $categoryService;
        $this->freelancerService = $freelancerService;
        $this->tagService = $tagService;
        $this->planService = $planService;
        $this->noticeService    = $noticeService;
    }

    public function index()
    {
        $services = $this->serviceService->index();
        $categories = $this->categoryService->index();
        $freelancers = $this->freelancerService->index([]);

        // Filter by single category
        if (request()->filled('category')) {
            $services = $services->filter(function ($service) {
                return $service->subCategory->category_id == request('category');
            });
        }

        // Filter by single freelancer
        if (request()->filled('freelancer')) {
            $services = $services->filter(function ($service) {
                return $service->user_id == request('freelancer');
            });
        }
        return view('pages.services.index', compact('services', 'categories', 'freelancers'));
    }
    public function create()
    {
        $categories = $this->categoryService->index();
        $tags = $this->tagService->getAllTags();
        $plans = $this->planService->index();
        $freelancers = $this->freelancerService->index([]);
        $currencies = Currency::all();

        return view('pages.services.create', compact('categories', 'tags', 'plans', 'freelancers', 'currencies'));
    }
    public function store(ServiceRequest $request)
    {

        $data = $request->validated();
        $currency = Currency::find($data['currency_id']);
        $exchangeRate = $currency->exchange_rate;

        foreach ($data['plans'] as &$plan) {
            foreach ($plan['features'] as &$feature) {
                if ($feature['type'] === 'price') {
                    $feature['value'] = number_format((float)$feature['value'] / $exchangeRate, 2, '.', '');
                }
            }
        }

        $service = $this->serviceService->create($data);
        $service->tags()->sync($data['tags'] ?? []);


        // one signal notification
        $user = $service->user;
        if ($user) {
            $titles = [
                'en' => __('new_service_added_title', [], 'en'),
                'ar' => __('new_service_added_title', [], 'ar'),
            ];

            $messages = [
                'en' => __('new_service_added_message', [], 'en'),
                'ar' => __('new_service_added_message', [], 'ar'),
            ];

            $this->noticeService->send(
                $user->id,
                $titles,
                $messages,
                'service',
                $service->id,
                true // 👈 service creation = informational, no unread badge needed
            );
        }
        // *********************************************//
        return redirect()->route('services.index')->with('success', __('service_created_successfully'));
    }
    public function edit($id)
    {
        $categories = $this->categoryService->index();
        $service = $this->serviceService->getServiceDetails($id);

        $tags = $service->tags()->pluck('tags.id')->toArray();
        $selectedTags = $tags;

        $servicePlans = $this->serviceService->getPlansByServiceId($id);
        $plans = $this->planService->index();
        $freelancers = $this->freelancerService->index([]);
        $currencies = Currency::all();

        $plansCount = Plan::count();
        return view('pages.services.edit', compact('selectedTags', 'categories', 'tags', 'plans', 'servicePlans', 'freelancers', 'service', 'plansCount',    'currencies'));
    }
    public function update(UpdateServiceRequest $request, $id)
    {
        $data = $request->validated();
        $currency = Currency::find($data['currency_id']);
        $exchangeRate = $currency->exchange_rate;

        foreach ($data['plans'] as &$plan) {
            foreach ($plan['features'] as &$feature) {
                if ($feature['type'] === 'price') {
                    $feature['value'] = number_format((float)$feature['value'] / $exchangeRate, 2, '.', '');
                }
            }
        }
        $this->serviceService->update($data, $id);
        $service = Service::findOrFail($id);
        $service->update([
            'user_id' => $data['user_id'] ?? $service->user_id, // لو موجود
        ]);
        $service->tags()->sync($data['tags'] ?? []);

        return redirect()->route('services.index')->with('success', __('service_updated_successfully'));
    }

    public function destroy($id)
    {
        $this->serviceService->delete($id);
        return redirect()->route('services.index')->with('success', __('service_deleted_successfully'));
    }

    public function toggleRecommended(Request $request)
    {
        // \Log::info('Request Data:', $request->all());

        $request->validate([
            'id' => 'required|exists:services,id',
        ]);

        $service = Service::findOrFail($request->id);
        $service->is_recommended = !$service->is_recommended;
        $service->save();

        // بدل redirect، نرجع JSON response
        return response()->json([
            'success' => true,
            'message' => __('service_updated_successfully'),
        ]);
    }
      public function toggleActivation(Request $request)
    {
        // \Log::info('Request Data:', $request->all());

        $request->validate([
            'id' => 'required|exists:services,id',
        ]);

        $service = Service::findOrFail($request->id);
        $service->is_active = !$service->is_active;
        $service->save();

        // بدل redirect، نرجع JSON response
        return response()->json([
            'success' => true,
            'message' => __('service_updated_successfully'),
        ]);
    }

    public function show($id)
    {
        $categories = $this->categoryService->index();
        $service = $this->serviceService->getServiceDetails($id);
        $tags = $service->tags()->pluck('tags.id')->toArray();
        $selectedTags = $tags;

        $servicePlans = $this->serviceService->getPlansByServiceId($id);
        $plans = $this->planService->index();
        $freelancers = $this->freelancerService->index([]);
        $currencies = Currency::all();

        $plansCount = Plan::count();
        return view('pages.services.show', compact('selectedTags', 'categories', 'tags', 'plans', 'servicePlans', 'freelancers', 'service', 'plansCount',    'currencies'));
    }

    public function getCategoriesByFreelancer($freelancerId)
    {
        $categories = $this->categoryService->getUserCategories($freelancerId);

        return response()->json([
            'status' => true,
            'data' => $categories->map(function ($cat) {
                return [
                    'id' => $cat->id,
                    'title' => $cat->translation->title,
                ];
            })
        ]);
    }
}
