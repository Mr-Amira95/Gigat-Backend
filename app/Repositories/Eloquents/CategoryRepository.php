<?php

namespace App\Repositories\Eloquents;

use App\Models\Category;
use App\Models\FreelancerCateogry;
use App\Utilities\FileManager;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class CategoryRepository implements CategoryRepositoryInterface
{
    protected $model;

    public function __construct(Category $category)
    {
        $this->model = $category;
    }

    public function index()
    {
        return $this->model->with('translations')->orderBy('id', 'desc')->get();
    }
    public function getUserCategories($freelancerId = null)
    {
        $userId = $freelancerId ?? auth()->id();

        $categoryIds = FreelancerCateogry::where('user_id', $userId)
            ->pluck('category_id');

        return Category::whereIn('id', $categoryIds)->get();
    }

    public function getUserCategoriesApi()
    {
        // dd(auth()->id());
        $categoryIds = FreelancerCateogry::where('user_id', Auth::guard('api')->id())
            ->pluck('category_id');

        return Category::whereIn('id', $categoryIds)->get();
    }
    public function getAllActive($isPopular = null)
    {
        $query = Category::query();

        if ($isPopular) {
            $query->where('is_popular', true);
        }

        return $query->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function store($data)
    {
        $category = $this->model->create([
            'icon' => FileManager::upload('categories', $data['icon']),
        ]);

        foreach (['en', 'ar'] as $locale) {
            $category->translations()->create([
                'language' => $locale,
                'title' => $data["title_$locale"],
                'description' => $data["description_$locale"],
            ]);
        }

        return $category;
    }

    public function update($id, $data)
    {
        $category = $this->find($id);

        $category->update([
            'icon' => FileManager::update('categories', $data['icon'], $category->icon),
        ]);

        foreach (['en', 'ar'] as $locale) {
            $category->translations()
                ->where('language', $locale)
                ->update([
                    'title' => $data["title_$locale"],
                    'description' => $data["description_$locale"],
                ]);
        }

        return $category;
    }

    public function delete($id)
    {
        $category = $this->find($id);
        FileManager::delete($category->icon);
        return $category->delete();
    }
    public function updateActivation(int $id)
    {
        $category = $this->find($id);
        $category->update(['is_active' => !$category->is_active]);
        return $category->fresh();
    }
    public function updatePopularStatus(int $id)
    {
        $category = Category::findOrFail($id);
        $category->is_popular = !$category->is_popular;
        $category->save();

        return $category;
    }
}
