<?php

namespace App\Utilities;

use Google\Cloud\Translate\V3\Client\TranslationServiceClient;
use Google\Cloud\Translate\V3\DetectLanguageRequest;
use Google\Cloud\Translate\V3\TranslateTextRequest;

class GoogleTranslator
{
    protected $client;
    protected $projectId;

    public function __construct()
    {
        $this->projectId = config('services.google.project_id');
        $path = base_path(config('services.google.credentials'));

        $this->client = new TranslationServiceClient([
            'credentials' => $path,
            'transport'   => 'rest',
        ]);
    }

    public function translate(string $text, string $target): string
    {
        $formattedParent = $this->client->locationName($this->projectId, 'global');

        $request = new TranslateTextRequest([
            'contents' => [$text],
            'target_language_code' => $target,
            'parent' => $formattedParent,
        ]);

        $response = $this->client->translateText($request);

        return $response->getTranslations()[0]->getTranslatedText();
    }

    public function detectLanguage(string $text): string
    {
        $formattedParent = $this->client->locationName($this->projectId, 'global');

        $request = new DetectLanguageRequest([
            'content' => $text,
            'parent'  => $formattedParent,
        ]);

        $response = $this->client->detectLanguage($request);

        return $response->getLanguages()[0]->getLanguageCode();
    }

    public function translateForStorage(string $text): array
    {
        $sourceLang = $this->detectLanguage($text);

        if ($sourceLang === 'en') {
            // Original English → translate to Arabic
            return [
                'en' => $text,
                'ar' => $this->translate($text, 'ar'),
            ];
        }

        if ($sourceLang === 'ar') {
            // Original Arabic → translate to English
            return [
                'ar' => $text,
                'en' => $this->translate($text, 'en'),
            ];
        }

        // Any other language → translate to both English & Arabic
        return [
            'en' => $this->translate($text, 'en'),
            'ar' => $this->translate($text, 'ar'),
        ];
    }
}
