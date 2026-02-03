<?php

namespace App\Repositories\Eloquents;

use App\Models\Faq;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use App\Repositories\Interfaces\FaqRepositoryInterface;
use App\Utilities\FileManager;

class FaqRepository implements FaqRepositoryInterface
{
    protected $model;

    public function __construct(Faq $faq)
    {
        $this->model = $faq;
    }
    public function index($categoryId = null)
    {
        $query = $this->model->with('faqable')->orderByDesc('id');

        if ($categoryId && $categoryId !== 'general') {
            $query->where('faqable_id', $categoryId);
        } elseif ($categoryId === 'general') {
            $query->whereNull('faqable_id');
        }

        return $query->get();
    }

    public function find($id)
    {
        return $this->model->with('faqable')->findOrFail($id);
    }
    public function store($data)
    {
        $faq = $this->model->create([
            'faqable_type' => (isset($data['category_id']) || $data['category_id'] != 'general') ? Category::class : null,
            'faqable_id' => (isset($data['category_id']) || $data['category_id'] != 'general') ?? null
        ]);

        foreach (['en', 'ar'] as $locale) {
            $translationData = [
                'language' => $locale,
                'question' => $data["question_$locale"],
                'answer' => $data["answer_$locale"]
            ];
            if (isset($data['media'])) {
               
                $translationData['media_path'] = FileManager::upload('faqs',$data['media']);
                $fileType = FileManager::getFileTypeFromPath($translationData['media_path']);
                $translationData['media_type'] = $fileType;
            }

            $faq->translations()->create($translationData);
        }

        return $faq;
    }

    public function update($id, $data)
    {
        $faq = $this->find($id);
        $faq->update([
            'faqable_type' => isset($data['category_id']) ? Category::class : null,
            'faqable_id' => $data['category_id'] ?? null
        ]);

        foreach (['en', 'ar'] as $locale) {
            $translationData = [
                'question' => $data["question_$locale"],
                'answer' => $data["answer_$locale"]
            ];

            if (isset($data['media_path']) && isset($data['media_type'])) {
                $translationData['media_path'] = FileManager::upload('faqs', $data['media']);
                $mimeType = FileManager::getMimeType($translationData['media_path']);
                $fileType = FileManager::getFileType($mimeType);
                $translationData['media_type'] = $fileType;
            }

            $faq->translations()->updateOrCreate(
                ['language' => $locale],
                $translationData
            );
        }

        return $faq;
    }

    public function delete($id)
    {
        $faq = $this->find($id);

        foreach ($faq->translations as $translation) {
            if ($translation->media_path) {
                Storage::disk('public')->delete($translation->media_path);
            }
        }

        return $faq->delete();
    }

    public function updateActivation($id)
    {
        $faq = $this->find($id);
        $faq->is_active = !$faq->is_active;
        $faq->save();
        return $faq;
    }

    public function getByCategory($categoryId)
    {
        return $this->model->where('faqable_type', Category::class)
        ->where('is_active',true)
            ->where('faqable_id', $categoryId)
            ->with('faqable')
            ->get();
    }
}
