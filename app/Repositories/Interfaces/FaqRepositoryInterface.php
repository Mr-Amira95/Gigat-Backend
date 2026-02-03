<?php

namespace App\Repositories\Interfaces;

interface FaqRepositoryInterface
{
    public function index($categoryId = null);
    public function find($id);
    public function store($data);
    public function update($id, $data);
    public function delete($id);
    public function updateActivation($id);
    public function getByCategory($categoryId);
} 