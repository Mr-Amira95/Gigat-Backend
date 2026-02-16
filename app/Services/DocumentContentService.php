<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Repositories\Interfaces\DocumentContentRepositoryInterface;

class DocumentContentService
{
    protected $documentContentRepository;

    public function __construct(DocumentContentRepositoryInterface $documentContentRepository)
    {
        $this->documentContentRepository = $documentContentRepository;
    }

    /**
     * Get all documents (Admin)
     */
    public function index()
    {
        return $this->documentContentRepository->index();
    }

    /**
     * Find document for edit
     */
    public function find($id)
    {
        return $this->documentContentRepository->find($id);
    }

    /**
     * Store document
     */
    public function store($data)
    {
        DB::beginTransaction();

        try {
            $document = $this->documentContentRepository->create($data);

            DB::commit();
            return $document;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update document
     */
    public function update($id, $data)
    {
        DB::beginTransaction();

        try {
            $document = $this->documentContentRepository->update($id, $data);

            DB::commit();
            return $document;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete document
     */
    public function delete($id)
    {
        DB::beginTransaction();

        try {
            $deleted = $this->documentContentRepository->delete($id);

            DB::commit();
            return $deleted;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
