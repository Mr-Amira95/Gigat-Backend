<?php

namespace App\Services;

use App\Repositories\Interfaces\FreelancerBankRepositoryInterface;

class FreelancerBankService
{
    protected $bankRepository;

    public function __construct(FreelancerBankRepositoryInterface $bankRepository)
    {
        $this->bankRepository = $bankRepository;
    }

    public function index($freelancerId)
    {
        return $this->bankRepository->index($freelancerId);
    }

    public function updateOrCreate($freelancerId, $data)
    {
        return $this->bankRepository->updateOrCreate($freelancerId, $data);
    }
}
