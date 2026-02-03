<?php

namespace App\Repositories\Interfaces;

interface SliderRepositoryInterface
{
    public function getAll();
    public function getAllByPlatform(string $platform);
    public function getAllActive();
    public function getActiveByPlatform(string $platform);
    public function getById(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function updateActivation(int $id);
}
