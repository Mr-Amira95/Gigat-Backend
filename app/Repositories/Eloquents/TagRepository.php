<?php

namespace App\Repositories\Eloquents;

use App\Models\Tag;
use App\Models\SubCategory;
use App\Repositories\Interfaces\TagRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

class TagRepository implements TagRepositoryInterface
{
    protected $tag;

    public function __construct(Tag $tag)
    {
        $this->tag = $tag;
    }

    public function getAllTags($params)
    {
        $query = $this->tag->query();
        if (isset($params)) {
            // dd($params);
            $query->where('sub_category_id', $params);
        }
        return $query->with('subCategory')->get();
    }

    // public function getAllTags($params)
    // {
    //     $query = $this->tag->query();

    //     if (!empty($params['subcategory_id'])) {
    //         $query->where('sub_category_id', $params['subcategory_id']);
    //     }

    //     if (!empty($params['category_id'])) {
    //         // join with subcategories to ensure category filter works
    //         $query->whereHas('subCategory', function ($q) use ($params) {
    //             $q->where('category_id', $params['category_id']);
    //         });
    //     }

    //     return $query->with('subCategory')->get();
    // }



    public function getTagById(int $id)
    {
        $tag = $this->tag->with(['subCategory'])->find($id);

        if (!$tag) {
            throw new ModelNotFoundException('Tag not found');
        }

        return $tag;
    }


    public function createTag(array $data)
    {
        $tags = [];

        foreach ($data['sub_category_id'] as $subCategoryId) {
            $tag = $this->tag->create([
                'sub_category_id' => $subCategoryId,
                'slug' => $data['slug'],
            ]);

            foreach ($data['translations'] as $language => $translation) {
                $tag->translations()->create([
                    'language' => $language,
                    'title' => $translation['title'],
                ]);
            }

            $tags[] = $tag->load(['translations', 'subCategory']);
        }

        return $tags;
    }


    public function updateTag(int $id, array $data)
    {
        $tag = $this->getTagById($id);

        $tag->update([
            'sub_category_id' => $data['sub_category_id'] ?? $tag->sub_category_id,
            'slug' => $data['slug'] ?? $tag->slug,
        ]);

        if (isset($data['translations'])) {
            foreach ($data['translations'] as $language => $translation) {
                $tag->translations()->updateOrCreate(
                    ['language' => $language],
                    ['title' => $translation['title']]
                );
            }
        }

        return $tag->load(['subCategory']);
    }

    public function deleteTag(int $id)
    {
        $tag = $this->getTagById($id);
        return $tag->delete();
    }
    public function getTagsByCategoryId(int $categoryId)
    {
        $subCategoryIds = SubCategory::where('category_id', $categoryId)->pluck('id');

        return $this->tag->whereIn('sub_category_id', $subCategoryIds)->get();
    }


    public function getTagsBySubcategoryId($subcategoryId)
    {
        return $this->tag->where('sub_category_id', $subcategoryId)->get();
    }
}
