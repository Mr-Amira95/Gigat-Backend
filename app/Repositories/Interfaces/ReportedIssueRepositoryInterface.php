<?php

namespace App\Repositories\Interfaces;

interface ReportedIssueRepositoryInterface
{
    public function create(array $data);
    public function index();
    public function updateStatus($id, string $status);
}
