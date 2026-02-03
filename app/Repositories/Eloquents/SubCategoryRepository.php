<?php

namespace App\Repositories\Eloquents;

use App\Models\SubCategory;
use App\Utilities\FileManager;
use App\Repositories\Interfaces\SubCategoryRepositoryInterface;

class SubCategoryRepository implements SubCategoryRepositoryInterface
{
    protected $model;

    public function __construct(SubCategory $subCategory)
    {
        $this->model = $subCategory;
    }

    public function index($categoryId = null)
    {
        $query = $this->model->with('category');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        } else {
            return $query->orderBy('id', 'desc')->get();
        }
        return $query->where('is_active',true)->orderBy('id', 'desc')->get();
    }
    public function subCategoriesByCategoryIds($categoryIds){
        return $this->model->whereIn('category_id',$categoryIds)->get();
    }
    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function store($data)
    {
        $subCategory = $this->model->create([
            'category_id' => $data['category_id'],
            'icon' => FileManager::upload('sub-categories', $data['icon']),
            'is_active' => $data['is_active'] ?? true
        ]);

        foreach (['en', 'ar'] as $locale) {
            $subCategory->translations()->create([
                'language' => $locale,
                'title' => $data["title_$locale"],
            ]);
        }

        return $subCategory;
    }

    public function update($id, $data)
    {
        $subCategory = $this->find($id);

        $subCategory->update([
            'category_id' => $data['category_id'],
            'icon' => isset($data['icon'])
                ? FileManager::update('sub-categories', $data['icon'], $subCategory->icon)
                : $subCategory->icon,
            'is_active' => $data['is_active'] ?? $subCategory->is_active
        ]);

        foreach (['en', 'ar'] as $locale) {
            $subCategory->translations()
                ->where('language', $locale)
                ->update([
                    'title' => $data["title_$locale"]
                ]);
        }

        return $subCategory;
    }

    public function delete($id)
    {
        $subCategory = $this->find($id);
        FileManager::delete($subCategory->icon);
        return $subCategory->delete();
    }
    public function updateActivation(int $id)
    {
        $subCategory = $this->find($id);
        $subCategory->update(['is_active' => !$subCategory->is_active]);
        return $subCategory->fresh();
    }
}
