<?php

namespace App\Repositories\Eloquents;

use App\Models\DocumentContent;
use App\Repositories\Interfaces\DocumentContentRepositoryInterface;

class DocumentContentRepository implements DocumentContentRepositoryInterface
{
    protected $model;

    public function __construct(DocumentContent $documentContent)
    {
        $this->model = $documentContent;
    }

    /**
     * Admin Listing
     */
    public function index()
    {
        return $this->model
            ->with(['translation', 'category.translation'])
            ->latest()
            ->get();
    }

    /**
     * Find for edit
     */
    public function find($id)
    {
        return $this->model
            ->with(['translations', 'category.translation'])
            ->findOrFail($id);
    }

    /**
     * Create document with translations
     */
    public function create($data)
    {
        $document = $this->model->create([
            'document_category_id' => $data['document_category_id'],
        ]);

        foreach (['en', 'ar'] as $locale) {
            $document->translations()->create([
                'language' => $locale,
                'content'  => $data["content_$locale"],
                'title'  => $data["title_$locale"],
            ]);
        }

        return $document->load(['translation', 'category.translation']);
    }

    /**
     * Update document + translations
     */
    public function update($id, $data)
    {
        $document = $this->model->findOrFail($id);

        $document->update([
            'document_category_id' => $data['document_category_id'],
        ]);

        foreach (['en', 'ar'] as $locale) {
            $document->translations()
                ->where('language', $locale)
                ->update([
                'content' => $data["content_$locale"],
                'title' => $data["title_$locale"],
                ]);
        }

        return $document->load(['translation', 'category.translation']);
    }

    /**
     * Delete document
     */
    public function delete($id)
    {
        $document = $this->model->findOrFail($id);
        return (bool) $document->delete();
    }
}
