<?php

namespace App\Repositories\Interfaces;

interface SubCategoryRepositoryInterface
{
    public function index();
    public function subCategoriesByCategoryIds($categoryIds);
    public function find($id);
    public function store($data);
    public function update($id, $data);
    public function delete($id);
    public function updateActivation(int $id);
}
