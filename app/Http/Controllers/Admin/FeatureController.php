<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Services\PlanService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FixedFeatureRequest;

class FeatureController extends Controller
{
    protected $planService;

    public function  __construct(PlanService $planService)
    {
        $this->planService = $planService;
    }
    public function index(Request $request){
        $planId = $request->query('plan_id');
        $plans = $this->planService->index();
        $features = $this->planService->getAllFixedFeatures($planId);
        return view('pages.features.index',compact('plans', 'features'));
    }
    public function create(){
        $plans = $this->planService->index();
        return view('pages.features.create',compact('plans'));
    }
    public function store(FixedFeatureRequest $request){
        $this->planService->createFixedFeature($request->validated());
        return redirect()->route('features.index')
            ->with('success', __('features_created_successfully'));
    }
    public function edit($id){
        $plans = $this->planService->index();
        $feature = $this->planService->fixedFeatureDetails($id);
        return view('pages.features.edit',compact('plans','feature'));
    }
    public function update(FixedFeatureRequest $request, $id)
    {
        $this->planService->updateFixedFeature($request->validated(), $id);
        return redirect()->route('features.index')
            ->with('success', __('features_updated_successfully'));
    }

    public function delete($id)
    {
        $this->planService->deleteFixedFeature($id);
        return redirect()->route('features.index')
            ->with('success', __('features_deleted_successfully'));
    }
}
