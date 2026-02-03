<?php

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use App\Services\ServiceService;
use App\Services\CategoryService;
use App\Http\Resources\TagResource;
use App\Http\Controllers\Controller;
use App\Services\SubCategoryService;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\FaqResource;
use App\Http\Resources\SubCategoryResource;
use App\Services\FaqService;
use App\Services\TagService;

class CategoryController extends Controller
{
    protected $categoryService;
    protected $subCategoryService;
    protected $serviceService;
    protected $tagService;
    protected $faqService;
    public function __construct(CategoryService $categoryService, SubCategoryService $subCategoryService, ServiceService $serviceService, TagService $tagService, FaqService $faqService)
    {
        $this->categoryService = $categoryService;
        $this->subCategoryService = $subCategoryService;
        $this->serviceService = $serviceService;
        $this->tagService = $tagService;
        $this->faqService = $faqService;
    }
    public function index(Request $request)
    {
        try {
            $isPopular = $request->query('is_popular', null);
            $categories = $this->categoryService->getAllActive($isPopular);
            return $this->successResponse(__('categories_retrieved_successfully'), CategoryResource::collection($categories));
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }
    public function details($id)
    {
        $data = [
            'category' => new CategoryResource($this->categoryService->find($id)),
            'sub_categories' => SubCategoryResource::collection($this->subCategoryService->index($id)),
            // 'featured_services' => ServiceResource::collection($this->serviceService->getFeaturedServices()['data']),
            'featured_services' => ServiceResource::collection($this->serviceService->getRelatedServicesByCategory($id)),
            'tags' => TagResource::collection($this->tagService->getTagsByCategoryId($id)),
            'faqs' => FaqResource::collection($this->faqService->getByCategory($id))
        ];
        return $this->successResponse(__('success'), $data);
    }
}
