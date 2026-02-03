<?php

namespace App\Services;

use App\Models\Category;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Repositories\Interfaces\SubCategoryRepositoryInterface;

class SubCategoryService
{
    protected $categoryService;
    protected $subCategoryRepository;

    public function __construct(SubCategoryRepositoryInterface $subCategoryRepository, CategoryService $categoryService)
    {
        $this->subCategoryRepository = $subCategoryRepository;
        $this->categoryService = $categoryService;
    }

    public function index($categoryId = null)
    {
        return $this->subCategoryRepository->index($categoryId);
    }
    public function subCategoriesByCategoryIds($categoryIds) {
        return $this->subCategoryRepository->subCategoriesByCategoryIds($categoryIds);
    }
    public function find($id)
    {
        return $this->subCategoryRepository->find($id);
    }

    public function store($data)
    {
        try {
            DB::beginTransaction();
            $result = $this->subCategoryRepository->store($data);
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
            $result = $this->subCategoryRepository->update($id, $data);
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

            $subCategory = $this->subCategoryRepository->find($id);
            $subCategory->load('services', 'tags');
            if ($subCategory->tags->isNotEmpty()) {
                throw new Exception(__('subcategory_has_tags') . '|' . $id);
            }
            if ($subCategory->services->isNotEmpty()) {
                throw new Exception(__('subcategory_has_services') . '|' . $id);
            }

            $result = $this->subCategoryRepository->delete($id);
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
            $subCategory = $this->find($id);
            $category = $this->categoryService->find($subCategory->category_id);
            throw_if(!$category->is_active, Exception::class, __('subcategory_activation_failed_category_inactive'));

            $subCategory = $this->subCategoryRepository->updateActivation($id);

            return $subCategory;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
