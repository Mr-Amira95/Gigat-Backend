<?php

namespace App\Repositories\Interfaces;

interface ProfessionRepositoryInterface
{
    public function index();
    public function getAllActive();
    public function find($id);
    public function store($data);
    public function update($id, $data);
    public function delete($id);
    public function updateActivation($id);
} 
