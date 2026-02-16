<?php

namespace App\Http\Controllers\File;

use App\Http\Controllers\Controller;
use App\Models\DocumentCategory;
use App\Models\DocumentContent;

class SystemDocsController extends Controller
{
    public function index()
    {
        $categories = DocumentCategory::with([
            'translation',
            'children.translation',
            'children.documents.translation'
        ])
            ->whereNull('parent_id')
            ->get();

        return view('system-docs.index', compact('categories'));
    }

    public function show($documentId)
    {
        $categories = DocumentCategory::with([
            'translation',
            'children.translation',
            'children.documents.translations'
        ])
            ->whereNull('parent_id')
            ->get();

        $document = DocumentContent::with([
            'translations',
            'category.translation',
            'category.parent.translation'
        ])
            ->findOrFail($documentId);

        $category = $document->category;
        $subCategory = $category?->parent; // if exists

        return view('system-docs.index', compact(
            'categories',
            'document',
            'category',
            'subCategory'
        ));
    }
}
