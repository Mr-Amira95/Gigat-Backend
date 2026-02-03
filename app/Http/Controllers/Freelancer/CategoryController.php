<?php

namespace App\Http\Controllers\Freelancer;

use Exception;
use Illuminate\Http\Request;
use App\Services\CategoryService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        $categories = $this->categoryService->index();
        return view('pages.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('pages.categories.create');
    }

    public function store(CategoryRequest $request)
    {
        try {
            $this->categoryService->store($request->validated());
            return redirect()->route('categories.index')
                ->with('success', __('category_created_successfully'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }
    public function edit($id)
    {
        $category = $this->categoryService->find($id);
        return view('pages.categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        try {
            $this->categoryService->update($id, $request->validated());
            return redirect()->route('categories.index')
                ->with('success', __('category_updated_successfully'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $this->categoryService->delete($id);
            return redirect()->back()
                ->with('success', __('category_deleted_successfully'));
        } catch (Exception $e) {
            return redirect()->route('categories.index')->with('error_message', $e->getMessage());
        }
    }

    public function show($id)
    {
        $category = $this->categoryService->find($id);
        return view('pages.categories.show', compact('category'));
    }
    public function updateActivation(Request $request)
    {
        try {
            $category = $this->categoryService->updateActivation($request->id);
            return $this->successResponse('success');
        } catch (Exception $e) {
            return $this->ErrorResponse($e->getMessage());
        }
    }

    public function updatePopularStatus(Request $request)
    {
        try {
            $category = $this->categoryService->updatePopularStatus($request->id);
            return $this->successResponse(__('status_updated_successfully'));
        } catch (Exception $e) {
            return $this->ErrorResponse($e->getMessage());
        }
    }
}
