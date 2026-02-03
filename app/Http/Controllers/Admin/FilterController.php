<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Services\FilterService;
use App\Services\CategoryService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FilterRequest;

class FilterController extends Controller
{
    protected $filterService;
    protected $categoryService;

    public function __construct(FilterService $filterService, CategoryService $categoryService)
    {
        $this->filterService = $filterService;
        $this->categoryService = $categoryService;
    }

    public function index(Request $request)
    {
        $filters = $this->filterService->index($request->category_id);
        $categories = $this->categoryService->index();
        return view('pages.filters.index', compact('filters', 'categories'));
    }
    public function create()
    {
        $categories = $this->categoryService->index();
        return view('pages.filters.create', compact('categories'));
    }
    public function store(FilterRequest $request)
    {
        try {
            $this->filterService->store($request->validated());
            return redirect()->route('filters.index')
                ->with('success', __('filter_created_successfully'));
        } catch (Exception $e) {
            Log::error($e);
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id)
    {
        $filter = $this->filterService->find($id);
        $categories = $this->categoryService->index();
        return view('pages.filters.edit', compact('filter', 'categories'));
    }

    public function update(FilterRequest $request, $id)
    {
        try {
            $this->filterService->update($id, $request->validated());
            return redirect()->route('filters.index')
                ->with('success', __('filter_updated_successfully'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $this->filterService->delete($id);
            return redirect()->back()
                ->with('success', __('filter_deleted_successfully'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
}
