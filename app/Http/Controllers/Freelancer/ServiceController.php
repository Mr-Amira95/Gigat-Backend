<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
// use App\Http\Requests\Admin\ServiceRequest;
// use App\Http\Requests\Admin\UpdateServiceRequest;
use App\Http\Requests\Freelancer\ServiceRequest;
use App\Http\Requests\Freelancer\UpdateServiceRequest;
use App\Models\Currency;
use App\Models\Plan;
use App\Models\Service;
use App\Services\CategoryService;
use App\Services\FreelancerService;
use App\Services\MetaConversionsApiService;
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
    public function __construct(ServiceService $serviceService, CategoryService $categoryService, FreelancerService $freelancerService, TagService $tagService, PlanService $planService)
    {
        $this->serviceService = $serviceService;
        $this->categoryService = $categoryService;
        $this->freelancerService = $freelancerService;
        $this->tagService = $tagService;
        $this->planService = $planService;
    }

    public function index()
    {
        $freelancerId = auth()->guard('freelancer')->id();
        $services = $this->serviceService->getForFreelancer($freelancerId);
        $categories = $this->categoryService->index();
        $freelancers = $this->freelancerService->index([]);
        return view('pages-freelancer.services.index', compact('services', 'categories', 'freelancers'));
    }
    public function create()
    {
        $freelancerId = auth()->id();
        $categories = $this->categoryService->getUserCategories($freelancerId);
        $tags = $this->tagService->getAllTags();
        $plans = $this->planService->index();
        $freelancers = $this->freelancerService->index([]);
        $currencies = Currency::all();

        return view('pages-freelancer.services.create', compact('categories', 'tags', 'plans', 'freelancers', 'currencies'));
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

        app(MetaConversionsApiService::class)->dispatchEvent(auth()->user(), $request, 'Freelancer Create Service');

        return redirect()->route('freelancer.services.index')->with('success', __('service_created_successfully'));
    }
    public function edit($id)
    {
        $freelancerId = auth()->id();
        $categories = $this->categoryService->getUserCategories($freelancerId);
        $service = $this->serviceService->getServiceDetails($id);
        $tags = $service->tags()->pluck('tags.id')->toArray();
        $selectedTags = $service->tags()->pluck('tags.id')->toArray();

        $servicePlans = $this->serviceService->getPlansByServiceId($id);
        $plans = $this->planService->index();
        $freelancers = $this->freelancerService->index([]);
        $currencies = Currency::all();

        $plansCount = Plan::count();
        return view('pages-freelancer.services.edit', compact('selectedTags', 'categories', 'tags', 'plans', 'servicePlans', 'freelancers', 'service', 'plansCount',    'currencies'));
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
        // احفظ التعديلات
        $service = $this->serviceService->update($data, $id);
        $service = Service::findOrFail($id);
        $service->update([
            'user_id' => $data['user_id'] ?? $service->user_id, // لو موجود
        ]);
        $service->tags()->sync($data['tags'] ?? []);

        return redirect()->route('freelancer.services.index')->with('success', __('service_updated_successfully'));
    }

    public function destroy($id)
    {
        $this->serviceService->delete($id);
        return redirect()->route('freelancer.services.index')->with('success', __('service_deleted_successfully'));
    }


    public function toggleRecommended(Request $request)
    {
        abort(403, 'Admin only');
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
        return view('pages-freelancer.services.show', compact('selectedTags', 'categories', 'tags', 'plans', 'servicePlans', 'freelancers', 'service', 'plansCount',    'currencies'));
    }
}
