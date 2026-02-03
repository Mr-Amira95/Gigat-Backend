<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Repositories\Interfaces\PlanRepositoryInterface;

class PlanService
{
    protected $planRepository;

    public function __construct(PlanRepositoryInterface $planRepository)
    {
        $this->planRepository = $planRepository;
    }

    public function index()
    {
        return $this->planRepository->index();
    }

    public function find($id)
    {
        return $this->planRepository->find($id);
    }

    public function store($data)
    {
        try {
            DB::beginTransaction();
            $result = $this->planRepository->store($data);
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update($id, $data)
    {
        try {
            DB::beginTransaction();
            $result = $this->planRepository->update($id, $data);
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();
            $result = $this->planRepository->delete($id);
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public function getFeaturesByPlan($id)
    {
        return $this->planRepository->getFeaturesByPlan($id);
    }
    public function getAllFixedFeatures($planId)
    {
        return $this->planRepository->getAllFixedFeatures($planId);
    }
    public function createFixedFeature($data){
        return $this->planRepository->createFixedFeature($data);
    }
    public function fixedFeatureDetails($id){
        return $this->planRepository->fixedFeatureDetails($id);
    }
    public function updateFixedFeature(array $data, int $id)
    {
        return $this->planRepository->updateFixedFeature($data, $id);
    }

    public function deleteFixedFeature(int $id)
    {
        return $this->planRepository->deleteFixedFeature($id);
    }
}
