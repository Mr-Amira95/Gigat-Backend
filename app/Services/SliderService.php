<?php

namespace App\Services;

use Exception;
use App\Repositories\Interfaces\SliderRepositoryInterface;
use Illuminate\Support\Facades\DB;

class SliderService
{
    protected $repository;

    public function __construct(SliderRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAllActive()
    {
        try {
            return $this->repository->getAllActive();
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function getAllByPlatform(string $platform)
    {
        try {
            return $this->repository->getAllByPlatform($platform);
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function getActiveByPlatform(string $platform)
    {
        try {
            return $this->repository->getActiveByPlatform($platform);
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function create(array $data)
    {
        try {
            DB::beginTransaction();

            $slider = $this->repository->create($data);

            DB::commit();

            return $slider;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update(int $id, array $data)
    {
        try {
            DB::beginTransaction();

            $slider = $this->repository->update($id, $data);

            DB::commit();

            return $slider;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
   
    public function delete(int $id)
    {
        try {
            DB::beginTransaction();

            $result = $this->repository->delete($id);

            DB::commit();

            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getById(int $id)
    {
        try {
            return $this->repository->getById($id);
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function updateActivation($id)
    {
        try {
            $slider = $this->repository->updateActivation($id);
            return $slider;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
