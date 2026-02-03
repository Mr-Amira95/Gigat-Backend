<?php

namespace App\Repositories\Eloquents;

use App\Models\RequestFeedback;
use App\Repositories\Interfaces\RequestFeedbackRepositoryInterface;
use App\Utilities\FileManager;
use App\Utilities\GoogleTranslator;

class RequestFeedbackRepository implements RequestFeedbackRepositoryInterface
{
    protected $model;
    protected $googleTranslator;

    public function __construct(RequestFeedback $model, GoogleTranslator $googleTranslator)
    {
        $this->model = $model;
        $this->googleTranslator = $googleTranslator;
    }

    public function create($data)
    {
        // 1. Create main feedback record
        $feedback = $this->model->create([
            'request_id' => $data['request_id'],
        ]);

        // 2. Translate message EN/AR
        $translations = $this->googleTranslator->translateForStorage($data['message']);

        foreach (['en', 'ar'] as $lang) {
            $feedback->translations()->create([
                'language' => $lang,
                'message'  => $translations[$lang],
            ]);
        }

        // 3. Upload attachments to /uploads/requests/{id}/feedback
        if (!empty($data['attachments']) && is_array($data['attachments'])) {
            foreach ($data['attachments'] as $file) {
                $mediaPath = FileManager::upload("requests/{$data['request_id']}/feedback", $file);

                $feedback->attachments()->create([
                    'attachment_path' => $mediaPath,
                ]);
            }
        }

        return $feedback->fresh();
    }
}
