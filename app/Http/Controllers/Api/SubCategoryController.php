<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\SubCategoryService;
use App\Http\Resources\SubCategoryResource;

class SubCategoryController extends Controller
{
   protected $subCategoryService;   

   public function __construct(SubCategoryService $subCategoryService)
   {
      $this->subCategoryService = $subCategoryService;
   }    

   public function getByCategoryId(Request $request)
   {
      $subCategories = $this->subCategoryService->index($request->category_id);
      return $this->successResponse(__('success'), SubCategoryResource::collection($subCategories));
   }

}
