<?php

namespace App\Repositories\Interfaces;

interface CompanyRepositoryInterface
{
    public function registerCompany($data);
    public function index($params);
    public function store($data);
    public function find($id);
    public function update($id, $data);
    public function getFreelancerCompanyId($freelancerUserId = null);
    public function checkFreelancerOwnsCompany($companyId, $freelancerUserId = null);
}
