<?php

namespace App\Repositories\Interfaces;

interface RoleRepositoryInterface
{
    public function index();
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function find($id);
} 