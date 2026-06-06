<?php

namespace App\Services;

use App\Repositories\Interfaces\FinanceRepositoryInterface;

class FinanceService
{

    protected $financeRepository;

    public function __construct(FinanceRepositoryInterface $financeRepository)
    {
        $this->financeRepository = $financeRepository;
    }

    public function getAll()
    {
        return $this->financeRepository->getAll();
    }
    public function getForFreelancer()
    {
        return $this->financeRepository->getForFreelancer();
    }
    public function getClientFinancialRecords()
    {
        return $this->financeRepository->getClientFinancialRecords();
    }
      public function getFreelancerFinancialRecords()
    {
        return $this->financeRepository->getFreelancerFinancialRecords();
    }
    public function bulkUpdate(array $data): void
    {
        $financeIds = $data['finance_ids'];
        $this->financeRepository->markAsPaid($financeIds);
    }

    public function getAllFiltered(array $filters, ?int $perPage = 50)
    {
        return $this->financeRepository->getAllFiltered($filters, $perPage);
    }
}
