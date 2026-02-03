<?php

namespace App\Repositories\Interfaces;

interface FreelancerRepositoryInterface
{
    public function index($params);
    public function store(array $data);
    public function delete($id);
    public function updateActivation($id);
    public function updateVerification($id);
    public function find($id);
    public function completeProfile(array $data);
    public function getUserProfile($id);
    public function updateProfile($id, array $data);
    public function getArchived();
    public function restore($id);
}
