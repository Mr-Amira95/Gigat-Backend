<?php

namespace App\Repositories\Interfaces;

interface ClientRepositoryInterface
{
    public function index($params);
    public function store($data);
    public function delete($id);
    public function updateActivation($id);
    public function find($id);
    public function getUserProfile($id);
    public function updateProfile($id, array $data);
    public function getArchived();
    public function restore($id);
}
