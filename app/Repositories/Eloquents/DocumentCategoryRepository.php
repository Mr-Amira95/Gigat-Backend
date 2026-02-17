<?php

namespace App\Repositories\Eloquents;

use App\Models\DocumentCategory;
use App\Repositories\Interfaces\DocumentCategoryRepositoryInterface;

class DocumentCategoryRepository implements DocumentCategoryRepositoryInterface
{
    protected $model;

    public function __construct(DocumentCategory $documentCategory)
    {
        $this->model = $documentCategory;
    }

    /**
     * Admin Listing (Tree Root Categories)
     */
    public function index()
    {
        return $this->model
            ->with(['children.translation', 'translation'])
            ->whereNull('parent_id')
            ->latest()
            ->get();
    }

    /**
     * Find for edit
     */
    public function find($id)
    {
        return $this->model
            ->with('translations')
            ->findOrFail($id);
    }

    /**
     * Get parent categories
     */
    public function getParents()
    {
        return $this->model
            ->whereNull('parent_id')
            ->with('translation')
            ->get();
    }

    /**
     * Create category with translations
     */
    public function create($data)
    {
        $category = $this->model->create([
            'parent_id' => $data['parent_id'] ?? null,
        ]);

        foreach (['en', 'ar'] as $locale) {
            $category->translations()->create([
                'language' => $locale,
                'name'     => $data["name_$locale"],
            ]);
        }

        return $category;
    }

    /**
     * Update category + translations
     */
    public function update($id, $data)
    {
        $category = $this->model->findOrFail($id);

        $category->update([
            'parent_id' => $data['parent_id'] ?? $category->parent_id,
        ]);

        foreach (['en', 'ar'] as $locale) {
            $category->translations()
                ->where('language', $locale)
                ->update([
                    'name' => $data["name_$locale"],
                ]);
        }

        return $category;
    }

    /**
     * Delete category
     */
    public function delete($id)
    {
        $category = $this->model->findOrFail($id);
        return (bool) $category->delete();
    }
}
