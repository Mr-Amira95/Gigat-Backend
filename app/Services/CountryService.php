<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Repositories\Interfaces\CountryRepositoryInterface;

class CountryService
{
    protected $countryRepository;

    public function __construct(CountryRepositoryInterface $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    public function index()
    {
        return $this->countryRepository->index();
    }
    public function getAllActive($perPage = null,$search = null)
    {
        return $this->countryRepository->getAllActive($perPage,$search);
    }
    public function find($id)
    {
        return $this->countryRepository->find($id);
    }

    public function store($data)
    {
        try {
            DB::beginTransaction();
            $result = $this->countryRepository->store($data);
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update($id, $data)
    {
        try {
            DB::beginTransaction();
            $result = $this->countryRepository->update($id, $data);
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();
            $result = $this->countryRepository->delete($id);
            DB::commit();
            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateActivation($id)
    {
        try {
            $country = $this->countryRepository->updateActivation($id);
            return $country;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
