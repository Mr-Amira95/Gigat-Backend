<?php

namespace App\Repositories\Interfaces;

interface DocumentCategoryRepositoryInterface
{
    public function index();
    public function find($id);
    public function getParents();
    public function create($data);
    public function update($id, $data);
    public function delete($id);
}
