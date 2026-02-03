<?php

namespace App\Services;

use App\Repositories\Interfaces\AdminRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Exception;

class AdminService
{
    protected $adminRepository;

    public function __construct(AdminRepositoryInterface $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }

    public function index()
    {
        return $this->adminRepository->index();
    }

    public function create(array $data)
    {
        try {
            DB::beginTransaction();

            $admin = $this->adminRepository->create($data);

            DB::commit();
            return $admin;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update($id, array $data)
    {
        try {
            DB::beginTransaction();

            $admin = $this->adminRepository->update($id, $data);

            DB::commit();
            return $admin;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();

            $result = $this->adminRepository->delete($id);

            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function find($id)
    {
        return $this->adminRepository->find($id);
    }

    public function updateActivation($id)
    {
        try {
            $admin = $this->adminRepository->updateActivation($id);
            return $admin;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
