<?php

namespace App\Repositories\Interfaces;

use App\Models\Service;

interface ServiceRepositoryInterface
{
    public function index();
    public function getAllActive();
    public function find($id);
    public function getBySubCategory($subCategoryId, $perPage = null);
    public function getServicesByTag($tag, $perPage = null);
    public function getServicesByUserId($userId);
    public function getRecommendedServices($perPage = null);
    public function getFeaturedServices($perPage = null);
    public function getServiceDetails($serviceId);
    public function getRelatedServices($serviceId);
    public function getRelatedServicesByCategory($categoryId);
    public function getPlansByServiceId($serviceId);
    public function search($query, $subCategoryId, $perPage = null);
    public function getResultFilter($filters);
    public function create($data);
    public function update(array $data, Service $service);
    public function delete($id);
    public function deleteMedia($id);
    public function toggleActivation($id);
}
