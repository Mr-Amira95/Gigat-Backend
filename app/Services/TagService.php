<?php

namespace App\Services;

use Exception;
use App\Models\Tag;
use Illuminate\Support\Str;
use App\Repositories\Interfaces\TagRepositoryInterface;

class TagService
{
    protected $tagRepository;

    public function __construct(TagRepositoryInterface $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    public function getAllTags($params = null)
    {
        return $this->tagRepository->getAllTags($params);
    }

    public function getTagById(int $id)
    {
        return $this->tagRepository->getTagById($id);
    }

    public function createTag(array $data)
    {
        $slug = Str::slug($data['translations']['en']['title']);

        if (Tag::where('slug', $slug)->exists()) {
            throw new \Exception(__('duplicate_slug'));
        }

        $data['slug'] = $slug;

        return $this->tagRepository->createTag($data);
    }

    public function updateTag(int $id, array $data)
    {
        if (isset($data['translations']['en']['title'])) {
            $data['slug'] = Str::slug($data['translations']['en']['title']);
        }
        return $this->tagRepository->updateTag($id, $data);
    }

    public function deleteTag(int $id)
    {
        $tag = $this->getTagById($id);
        $tag->load('services');
        if($tag->services->isNotEmpty()){
            throw new Exception(__('tag_has_service'));
        }
        return $this->tagRepository->deleteTag($id);
    }

    public function getTagsByCategoryId(int $categoryId)
    {
        return $this->tagRepository->getTagsByCategoryId($categoryId);
    }

    public function getTagsBySubcategoryId($subcategoryId)
    {
        return $this->tagRepository->getTagsBySubcategoryId($subcategoryId);
    }
}
