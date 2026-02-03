<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TagService;
use Illuminate\Http\Request;

class TagController extends Controller
{
    protected $tagService;

    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }
    public function getAllTags(Request $request)
    {
        $subCategory = $request->query('subcategory_id');
        $tags = $this->tagService->getAllTags($subCategory);
        return $this->successResponse('success', $tags);
    }

    public function getTagBySubCategory($Id)
    {
        $tags = $this->tagService->getTagsBySubcategoryId($Id);
        return $this->successResponse('success', $tags);
    }
}
