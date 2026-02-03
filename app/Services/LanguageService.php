<?php

namespace App\Services;

use App\Repositories\Interfaces\LanguageRepositoryInterface;

class LanguageService
{
    protected $languageRepository;

    public function __construct(LanguageRepositoryInterface $languageRepository)
    {
        $this->languageRepository = $languageRepository;
    }

    public function getAllActive($perPage = null)
    {
        return $this->languageRepository->getAllActive($perPage);
    }
}
    