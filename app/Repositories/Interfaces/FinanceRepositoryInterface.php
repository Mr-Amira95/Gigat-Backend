<?php

namespace App\Repositories\Interfaces;

interface FinanceRepositoryInterface
{
    public function getAll();
    public function getClientFinancialRecords();
    public function getFreelancerFinancialRecords();
    public function markAsPaid(array $data);

    public function getAllFiltered(array $filters);
}
