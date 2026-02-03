<?php

namespace App\Repositories\Interfaces;

interface RequestRepositoryInterface
{
    public function getAll(array $filters = []);
    public function getByUser($perPage);
    public function getByFreelancer($perPage);
    public function createRequest(array $data);
    public function getRequestDetails($id);
    public function addComment($data);
    public function confirmRequest($id);
}
