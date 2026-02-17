<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DocumentContentRequest;
use App\Services\DocumentContentService;
use App\Services\DocumentCategoryService;
use Illuminate\Http\Request;

class DocumentContentController extends Controller
{
    protected $documentContentService;
    protected $documentCategoryService;

    public function __construct(
        DocumentContentService $documentContentService,
        DocumentCategoryService $documentCategoryService
    ) {
        $this->documentContentService = $documentContentService;
        $this->documentCategoryService = $documentCategoryService;
    }

    public function index()
    {
        $documents = $this->documentContentService->index();
        return view('pages.system-documents.content.index', compact('documents'));
    }

    public function create()
    {
        $categories = $this->documentCategoryService->index();
        return view('pages.system-documents.content.create', compact('categories'));
    }
    public function show($id)
    {
        $document = $this->documentContentService->find($id);

        return view(
            'pages.system-documents.content.show',
            compact('document')
        );
    }

    public function store(DocumentContentRequest $request)
    {
        $this->documentContentService->store($request->all());

        return redirect()->route('document-contents.index')
            ->with('success', __('document_created_successfully'));
    }

    public function edit($id)
    {
        $document = $this->documentContentService->find($id);
        $categories = $this->documentCategoryService->index();

        return view('pages.system-documents.content.edit', compact('document', 'categories'));
    }

    public function update(DocumentContentRequest $request, $id)
    {
        $this->documentContentService->update($id, $request->all());

        return redirect()->route('document-contents.index')
            ->with('success', __('document_updated_successfully'));
    }

    public function destroy($id)
    {
        $this->documentContentService->delete($id);

        return redirect()->back()
            ->with('success', __('document_deleted_successfully'));
    }
}
