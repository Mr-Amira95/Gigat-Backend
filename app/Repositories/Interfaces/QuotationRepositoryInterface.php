<?php

namespace App\Repositories\Interfaces;

interface QuotationRepositoryInterface
{
    public function getAll();
    public function store(array $data);
    public function findAll($perPage = null);
    public function findById(int $id);
    public function createQuotationComment(array $data);
    public function getCommentsByQuotationId(int $quotationId);
    public function getByUserId($perPage);
    public function getQuotationDetails($id);
    public function delete(int $id);
}
