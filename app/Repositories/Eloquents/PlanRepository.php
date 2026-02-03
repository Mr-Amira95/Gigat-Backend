<?php

namespace App\Repositories\Eloquents;

use App\Models\FixedFeature;
use App\Models\Plan;
use App\Repositories\Interfaces\PlanRepositoryInterface;
use Illuminate\Support\Facades\DB;

class PlanRepository implements PlanRepositoryInterface
{
    protected $model;
    public function __construct(Plan $plan)
    {
        $this->model = $plan;
    }
    public function index()
    {
        // dd($this->model->with('features')->get());
        return $this->model->with('features')->get();
    }
    public function find($id)
    {
        return $this->model->with('features')->findOrFail($id);
    }
    public function store($data)
    {
        try {
            DB::beginTransaction();
            $plan = $this->model->create([
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            foreach (['en', 'ar'] as $locale) {
                $plan->translations()->create([
                    'language' => $locale,
                    'title' => $data["title_$locale"],
                ]);
            }
            DB::commit();
            return $plan;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public function update($id, $data)
    {
        try {
            DB::beginTransaction();
            $plan = $this->find($id);
            $plan->update([
                'updated_at' => now(),
            ]);
            foreach (['en', 'ar'] as $locale) {
                $plan->translations()
                    ->updateOrCreate(
                        ['plan_id' => $plan->id, 'language' => $locale],
                        ['title' => $data["title_$locale"]]
                    );
            }
            DB::commit();
            return $plan;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public function delete($id)
    {
        $plan = $this->find($id);
        $plan->services()->detach();
        return $plan->delete();
    }
    public function updateActivation($id)
    {
        $plan = $this->find($id);
        $plan->is_active = !$plan->is_active;
        $plan->save();
        return $plan;
    }
    public function getFeaturesByPlan($id)
    {
        $plan = $this->model->find($id);
        return $plan->features;
    }
    public function getAllFixedFeatures($planId)
    {
        $query = FixedFeature::with('plan');
        if($planId){
            $query->where('plan_id',$planId);
        }
        return $query->get();
    }
    public function createFixedFeature($data)
    {
        if ($data['plan_id'] == 0) {
            $plans = $this->model->all();

            foreach ($plans as $plan) {
                FixedFeature::create([
                    'plan_id'   => $plan->id,
                    'title_en'  => $data['title_en'],
                    'title_ar'  => $data['title_ar'],
                ]);
            }
            return;
        }
        FixedFeature::create($data);
    }
    public function fixedFeatureDetails($id){
        return FixedFeature::find($id);
    }
    public function updateFixedFeature($data,$id)
    {
        $feature = FixedFeature::findOrFail($id);

        if ($data['plan_id'] == 0) {
            FixedFeature::where('title_en', $feature->title_en)
                ->orWhere('title_ar', $feature->title_ar)
                ->delete();

            $plans = Plan::all();
            foreach ($plans as $plan) {
                FixedFeature::create([
                    'plan_id' => $plan->id,
                    'title_en' => $data['title_en'],
                    'title_ar' => $data['title_ar'],
                ]);
            }
        } else {
            $feature->update($data);
        }
    }

    public function deleteFixedFeature($id)
    {
        $feature = FixedFeature::findOrFail($id);
        $feature->delete();
    }
}
