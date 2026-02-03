<?php

namespace App\Repositories\Interfaces;

interface CountryRepositoryInterface
{
    public function index();
    public function getAllActive($perPage = null,$search = null);
    public function find($id);
    public function store($data);
    public function update($id, $data);
    public function delete($id);
    public function updateActivation($id);
} 