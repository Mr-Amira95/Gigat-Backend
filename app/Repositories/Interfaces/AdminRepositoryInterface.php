<?php

namespace App\Repositories\Interfaces;

interface AdminRepositoryInterface
{
    public function index();
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function find($id);
    public function updateActivation($id);
}
