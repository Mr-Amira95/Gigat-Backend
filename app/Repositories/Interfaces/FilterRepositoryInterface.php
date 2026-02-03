<?php

namespace App\Repositories\Interfaces;

interface FilterRepositoryInterface
{
    public function index();
    public function find($id);
    public function store($data);
    public function update($id, $data);
    public function delete($id);
    public function getFiltersByCategoryId($categoryId);
}
