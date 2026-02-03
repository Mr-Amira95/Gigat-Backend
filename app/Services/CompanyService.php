<?php

namespace App\Services;

use App\Repositories\Interfaces\CompanyRepositoryInterface;

class CompanyService
{
    protected $companyRepository;

    public function __construct(CompanyRepositoryInterface $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    public function registerCompany($data)
    {
        return $this->companyRepository->registerCompany($data);
    }
    
    public function index($params = [])
    {
        return $this->companyRepository->index($params);
    }

    public function store($data)
    {
        return $this->companyRepository->store($data);
    }

    public function find($id)
    {
        return $this->companyRepository->find($id);
    }

    public function update($id, $data)
    {
        return $this->companyRepository->update($id, $data);
    }
    public function getFreelancerCompanyId($freelancerUserId = null)
    {
        return $this->companyRepository->getFreelancerCompanyId($freelancerUserId);
    }

    public function checkOwnership($companyId, $freelancerUserId = null)
    {
        return $this->companyRepository->checkFreelancerOwnsCompany($companyId, $freelancerUserId);
    }
}
