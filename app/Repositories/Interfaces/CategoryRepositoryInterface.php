<?php

namespace App\Repositories\Interfaces;

interface CategoryRepositoryInterface
{
    public function index();
    public function getAllActive($isPopular);
    public function find($id);
    public function store($data);
    public function update($id, $data);
    public function delete($id);
    public function updateActivation(int $id);
    public function updatePopularStatus(int $id);

}
