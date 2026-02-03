<?php

namespace App\Services;

use App\Repositories\Interfaces\ReportedIssueRepositoryInterface;
use App\Repositories\ReportedIssueRepository;

class ReportedIssueService
{
    protected ReportedIssueRepositoryInterface $repository;

    public function __construct(ReportedIssueRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function create(array $data, $userId = null)
    {
        $data['user_id'] = $userId;
        $data['status']  = 'pending';
// dd($userId);
        return $this->repository->create($data);
    }

    public function index($status = null)
    {
        return $this->repository->index($status);
    }

    public function updateStatus($id, string $status)
    {
        if (!in_array($status, ['resolved', 'cancelled'])) {
            throw new \InvalidArgumentException('Invalid status.');
        }
        return $this->repository->updateStatus($id, $status);
    }
}
