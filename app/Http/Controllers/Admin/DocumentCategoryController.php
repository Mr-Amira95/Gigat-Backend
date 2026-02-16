<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentCategory;
use App\Services\DocumentCategoryService;
use Illuminate\Http\Request;

class DocumentCategoryController extends Controller
{
    protected $documentCategoryService;

    public function __construct(DocumentCategoryService $documentCategoryService)
    {
        $this->documentCategoryService = $documentCategoryService;
    }

    public function index()
    {
        $categories = $this->documentCategoryService->index();
        return view('pages.system-documents.categories.index', compact('categories'));
    }

    public function create()
    {
        $parents = $this->documentCategoryService->getParents();
        return view('pages.system-documents.categories.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $this->documentCategoryService->store($request->all());
        return redirect()->route('document-categories.index')
            ->with('success', __('category_created_successfully'));
    }

    public function edit($id)
    {
        $category = $this->documentCategoryService->find($id);
        $parents = $this->documentCategoryService->getParents();

        return view('pages.system-documents.categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, $id)
    {
        $this->documentCategoryService->update($id, $request->all());
        return redirect()->route('document-categories.index')
            ->with('success', __('category_updated_successfully'));
    }

    public function destroy($id)
    {
        $this->documentCategoryService->delete($id);
        return redirect()->back()
            ->with('success', __('category_deleted_successfully'));
    }

    public function getChildren($id)
    {
        $children = DocumentCategory::where('parent_id', $id)
            ->with('translation')
            ->get();

        return response()->json($children);
    }
}
