<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Services\PlanService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PlanRequest;

class PlanController extends Controller
{
    protected $planService;
    public function __construct(PlanService $planService)
    {
        $this->planService = $planService;
    }
    public function index()
    {
        $plans = $this->planService->index();
        return view('pages.plans.index', compact('plans'));
    }
    public function create()
    {
        return view('pages.plans.create');
    }
    public function store(PlanRequest $request)
    {
        try {
            $this->planService->store($request->validated());
            return redirect()->route('plans.index')
                ->with('success', __('plan_created_successfully'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }
    public function edit($id)
    {
        $plan = $this->planService->find($id);
        return view('pages.plans.edit', compact('plan'));
    }
    public function update(PlanRequest $request, $id)
    {
        try {
            $this->planService->update($id, $request->validated());
            return redirect()->route('plans.index')
                ->with('success', __('plan_updated_successfully'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }
    public function destroy($id)
    {
        try {
            $this->planService->delete($id);
            return redirect()->back()
                ->with('success', __('plan_deleted_successfully'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
    public function getFeaturesByPlan($id)
    {
        $plan = $this->planService->find($id);
        $features = $this->planService->getFeaturesByPlan($id);
        return view('pages.plans.features', compact('plan', 'features'));
    }

}
