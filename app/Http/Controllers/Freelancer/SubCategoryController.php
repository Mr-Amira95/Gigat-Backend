<?php

namespace App\Http\Controllers\Freelancer;

use Exception;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\SubCategoryService;
use App\Services\CategoryService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SubCategoryRequest;

class SubCategoryController extends Controller
{
    protected $subCategoryService;
    protected $CategoryService;

    public function __construct(SubCategoryService $subCategoryService, CategoryService $CategoryService)
    {
        $this->subCategoryService = $subCategoryService;
        $this->CategoryService = $CategoryService;
    }

    public function index(Request $request)
    {
        $categories = $this->CategoryService->index();
        $subCategories = $this->subCategoryService->index($request->category_id);
        return view('pages.sub-categories.index', compact('subCategories', 'categories'));
    }
    public function subCategoriesByCategoryIds(Request $request)
    {
        $categoryIds = $request->input('category_ids', []);
        $subCategories = $this->subCategoryService->subCategoriesByCategoryIds($categoryIds);

        return response()->json([
            'status' => 'success',
            'data' => $subCategories->map(function ($sub) {
                return [
                    'id' => $sub->id,
                    'title' => $sub->translation->title ?? $sub->title,
                ];
            }),
        ]);
    }

    public function create()
    {
        $categories = $this->CategoryService->index();
        return view('pages.sub-categories.create', compact('categories'));
    }

    public function store(SubCategoryRequest $request)
    {
        try {
            $this->subCategoryService->store($request->validated());
            return redirect()->route('subCategories.index')
                ->with('success', __('sub_category_created_successfully'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id)
    {
        $subCategory = $this->subCategoryService->find($id);
        $categories = $this->CategoryService->index();
        return view('pages.sub-categories.edit', compact('subCategory', 'categories'));
    }

    public function update(SubCategoryRequest $request, $id)
    {
        try {
            $this->subCategoryService->update($id, $request->validated());
            return redirect()->route('subCategories.index')
                ->with('success', __('sub_category_updated_successfully'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $this->subCategoryService->delete($id);
            return redirect()->back()
                ->with('success', __('sub_category_deleted_successfully'));
        } catch (Exception $e) {
            return redirect()->route('subCategories.index')
                ->with('error_message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $subCategory = $this->subCategoryService->find($id);
        return view('pages.sub-categories.show', compact('subCategory'));
    }
    public function updateActivation(Request $request)
    {
        try {
            $subCategory = $this->subCategoryService->updateActivation($request->id);
            return $this->successResponse('success');
        } catch (Exception $e) {
            return $this->ErrorResponse($e->getMessage());
        }
    }
}
