<?php

namespace App\Repositories\Interfaces;

interface LanguageRepositoryInterface
{
    /**
     * Get all active languages
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllActive($perPage = null);
} 