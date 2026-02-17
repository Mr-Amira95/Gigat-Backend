<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Repositories\Interfaces\DocumentCategoryRepositoryInterface;

class DocumentCategoryService
{
    protected $documentCategoryRepository;

    public function __construct(DocumentCategoryRepositoryInterface $documentCategoryRepository)
    {
        $this->documentCategoryRepository = $documentCategoryRepository;
    }

    /**
     * Get all categories (Admin)
     */
    public function index()
    {
        return $this->documentCategoryRepository->index();
    }

    /**
     * Find category for edit
     */
    public function find($id)
    {
        return $this->documentCategoryRepository->find($id);
    }

    /**
     * Get parent categories
     */
    public function getParents()
    {
        return $this->documentCategoryRepository->getParents();
    }

    /**
     * Store category
     */
    public function store($data)
    {
        return $this->documentCategoryRepository->create($data);
    }

    /**
     * Update category
     */
    public function update($id, $data)
    {
        return $this->documentCategoryRepository->update($id, $data);
    }

    /**
     * Delete category
     */
    public function delete($id)
    {
        return $this->documentCategoryRepository->delete($id);
    }
}
