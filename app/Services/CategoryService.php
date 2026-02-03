<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Models\Category;

class CategoryService
{
    protected $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function index()
    {
        return $this->categoryRepository->index();
    }

    public function getUserCategories($freelancerId)
    {
        return $this->categoryRepository->getUserCategories($freelancerId);
    }

    public function getUserCategoriesApi()
    {
        return $this->categoryRepository->getUserCategoriesApi();
    }
    public function getAllActive($isPopular = null)
    {
        return $this->categoryRepository->getAllActive($isPopular);
    }

    public function find($id)
    {
        return $this->categoryRepository->find($id);
    }

    public function store($data)
    {
        try {
            DB::beginTransaction();
            $result = $this->categoryRepository->store($data);
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
            $result = $this->categoryRepository->update($id, $data);
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

            $category = $this->categoryRepository->find($id);
            $category->load('subCategories.services', 'quotations');

            if ($category->subCategories->isNotEmpty()) {
                $hasServices = $category->subCategories->contains(fn($subCategory) => $subCategory->services->isNotEmpty());

                if ($hasServices) {
                    throw new Exception(__('category_has_subcategories') . '|' . $id);
                }
            }
            if ($category->quotations->isNotEmpty()) {
                throw new Exception(__('category_has_quotations'));
            }

            $result = $this->categoryRepository->delete($id);
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
            $category = $this->categoryRepository->updateActivation($id);
            return $category;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function updatePopularStatus($id)
    {
        try {
            $category = $this->categoryRepository->updatePopularStatus($id);
            return $category;
        } catch (Exception $e) {
            throw $e;
        }
    }

}
