<?php

namespace App\Services;

use App\Models\Service;
use App\Models\SubCategory;
use App\Repositories\Interfaces\ServiceRepositoryInterface;

class ServiceService
{
    protected $serviceRepository;
    public function __construct(ServiceRepositoryInterface $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
    }
    public function index()
    {
        return $this->serviceRepository->index();
    }
    public function getAllActive()
    {
        return $this->serviceRepository->getAllActive();
    }
    public function getServiceById($id)
    {
        return $this->serviceRepository->find($id);
    }
    public function getForFreelancer($id)
    {
        return $this->serviceRepository->getForFreelancer($id);
    }


    public function deleteService($id)
    {
        return $this->serviceRepository->delete($id);
    }
    public function getBySubCategory($subCategoryId, $perPage = null)
    {
        return $this->serviceRepository->getBySubCategory($subCategoryId, $perPage);
    }
    public function getServicesByTag($tag, $perPage = null)
    {
        return $this->serviceRepository->getServicesByTag($tag, $perPage);
    }
    public function getServicesByUserId($userId, $perPage)
    {
        return $this->serviceRepository->getServicesByUserId($userId, $perPage);
    }
    public function getRecommendedServices($perPage = null)
    {
        return $this->serviceRepository->getRecommendedServices($perPage);
    }
    public function getFeaturedServices($perPage = null)
    {
        return $this->serviceRepository->getFeaturedServices($perPage);
    }
    public function getRelatedServices($serviceId)
    {
        return $this->serviceRepository->getRelatedServices($serviceId);
    }
    public function getRelatedServicesByCategory($categoryId)
    {
        return $this->serviceRepository->getRelatedServicesByCategory($categoryId);
    }

    public function getServiceDetails($serviceId)
    {
        return $this->serviceRepository->getServiceDetails($serviceId);
    }
    public function getPlansByServiceId($serviceId)
    {
        return $this->serviceRepository->getPlansByServiceId($serviceId);
    }
    public function search($query, $subCategoryId, $perPage = null)
    {
        return $this->serviceRepository->search($query, $subCategoryId, $perPage);
    }
    public function getResultFilter($filters)
    {
        return $this->serviceRepository->getResultFilter($filters);
    }
    public function find($id)
    {
        return $this->serviceRepository->find($id);
    }
    public function create($data)
    {
        return $this->serviceRepository->create($data);
    }
    public function update($data, $id)
    {
        $service = $this->find($id);
        return $this->serviceRepository->update($data, $service);
    }
    public function delete($id)
    {
        return $this->serviceRepository->delete($id);
    }
    public function deleteMedia($id)
    {
        return $this->serviceRepository->deleteMedia($id);
    }


    public function searchServicesAndSubCategories($search, $perPage)
    {
        // Services search
        $services = Service::with(['translations', 'tags.translations', 'media', 'user.profession'])
            ->where('is_active', true) // << MUST apply to the whole query
            ->where(function ($query) use ($search) {
                $query->whereHas('translations', function ($q) use ($search) {
                    $q->where('title', 'like', "%$search%")
                        ->orWhere('description', 'like', "%$search%");
                })
                    ->orWhereHas('tags.translations', function ($q) use ($search) {
                        $q->where('title', 'like', "%$search%");
                    });
            })
            ->paginate($perPage);

        // SubCategories search
        $subCategories = SubCategory::with(['translations', 'tags.translations'])
            ->whereHas('translations', function ($q) use ($search) {
                $q->where('title', 'like', "%$search%");
            })
            ->orWhereHas('tags.translations', function ($q) use ($search) {
                $q->where('title', 'like', "%$search%");
            })
            ->get();

        return [
            'services' => $services,
            'sub_categories' => $subCategories,
        ];
    }

    public function toggleActivation($id)
    {
        $service = $this->serviceRepository->toggleActivation($id);

        return [
            'service_id' => $service->id,
            'is_active'  => $service->is_active,
        ];
    }
}
