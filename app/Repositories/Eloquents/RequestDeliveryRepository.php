<?php

namespace App\Repositories\Eloquents;

use App\Models\RequestDelivery;
use App\Models\RequestDeliveryTranslation;
use App\Models\RequestDeliveryAttachment;
use App\Repositories\Interfaces\RequestDeliveryRepositoryInterface;
use App\Utilities\FileManager;
use App\Utilities\GoogleTranslator;

class RequestDeliveryRepository implements RequestDeliveryRepositoryInterface
{
    protected $model;
    protected $googleTranslator;


    public function __construct(RequestDelivery $model, GoogleTranslator $googleTranslator)
    {
        $this->model = $model;
        $this->googleTranslator = $googleTranslator;
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }

    public function create($data)
    {
        // Create main delivery record
        $delivery = $this->model->create([
            'request_id' => $data['request_id'],
        ]);

        // 1️⃣ Translate message using GoogleTranslator
        $translations = $this->googleTranslator->translateForStorage($data['message']);

        // 2️⃣ Create translation records for both languages
        foreach (['en', 'ar'] as $lang) {
            $delivery->translations()->create([
                'language' => $lang,
                'message'  => $translations[$lang],
            ]);
        }

        // 3. Add attachments using Request-style file manager
        if (!empty($data['attachments']) && is_array($data['attachments'])) {
            foreach ($data['attachments'] as $file) {

                // same path folder logic used in RequestRepository
                $mediaPath = FileManager::upload("requests/{$data['request_id']}/delivery", $file);

                $delivery->attachments()->create([
                    'attachment_path' => $mediaPath,
                ]);
            }
        }

        return $delivery->fresh();
    }

    public function update($delivery, $data)
    {

        // 1️⃣ Update translations (EN + AR)
        if (!empty($data['message'])) {
            $translations = $this->googleTranslator->translateForStorage($data['message']);

            foreach (['en', 'ar'] as $lang) {
                $delivery->translations()
                    ->where('language', $lang)
                    ->update(['message' => $translations[$lang]]);
            }
        }

        // 2️⃣ Add new attachments
        if (!empty($data['attachments']) && is_array($data['attachments'])) {
            foreach ($data['attachments'] as $file) {

                $mediaPath = FileManager::upload("requests/{$delivery->request_id}/delivery", $file);

                $delivery->attachments()->create([
                    'attachment_path' => $mediaPath,
                ]);
            }
        }


        return $delivery->fresh();
    }


    public function deleteAttachmentById($attachmentId)
    {
        $attachment = RequestDeliveryAttachment::find($attachmentId);

        if (!$attachment) {
            return false;
        }

        // Delete physical file
        if (file_exists(public_path($attachment->attachment_path))) {
            @unlink(public_path($attachment->attachment_path));
        }

        $attachment->delete();

        return true;
    }

    public function getDeliveriesByRequestId($requestId)
    {
        return $this->model
            ->with(['translations', 'attachments'])
            ->where('request_id', $requestId)
            ->get();
    }
}
