<?php

namespace App\Repositories\Interfaces;

interface PlanRepositoryInterface
{
    public function index();
    public function find($id);
    public function store($data);
    public function update($id, $data);
    public function delete($id);
    public function getFeaturesByPlan($id);
    public function getAllFixedFeatures($planId);
    public function createFixedFeature($data);
    public function fixedFeatureDetails($id);
    public function updateFixedFeature($data,$id);
    public function deleteFixedFeature($id);
}
