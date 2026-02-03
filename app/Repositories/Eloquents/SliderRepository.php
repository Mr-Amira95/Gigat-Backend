<?php

namespace App\Repositories\Eloquents;

use App\Models\Slider;
use App\Repositories\Interfaces\SliderRepositoryInterface;
use App\Utilities\FileManager;

class SliderRepository implements SliderRepositoryInterface
{
    protected $model;

    public function __construct(Slider $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->with('translations')->latest()->get();
    }

    public function getAllByPlatform(string $platform)
    {
        return $this->model
            ->where('platform', $platform)
            ->with('translations')
            ->orderBy('ordering', 'asc')
            ->get();
    }

    public function getAllActive()
    {
        return $this->model
            ->active()
            ->with('translations')
            ->latest()
            ->get();
    }

    public function getActiveByPlatform(string $platform)
    {
        return $this->model
            ->active()
            ->where('platform', $platform)
            ->with('translations')
            ->orderBy('ordering')
            ->get();
    }

    public function getById(int $id)
    {
        return $this->model->with('translations')->findOrFail($id);
    }

    public function create(array $data)
    {
        // $slider = $this->model->create([
        //     'is_active' => true,
        //     'platform' => $data['platform'],
        //     'button_action' => $data['button_action'] ?? null,
        // ]);
        // Find the max ordering for this platform
        $maxOrder = $this->model
            ->where('platform', $data['platform'])
            ->max('ordering');

        $nextOrder = $maxOrder ? $maxOrder + 1 : 1;

        // Create slider with ordering
        $slider = $this->model->create([
            'is_active'     => true,
            'platform'      => $data['platform'],
            'button_action' => $data['button_action'] ?? null,
            'ordering'      => $nextOrder,
        ]);

        foreach (['en', 'ar'] as $language) {
            $translationData = [];

            // Handle media file upload
            if (isset($data["media_path_{$language}"]) && $data["media_path_{$language}"] instanceof \Illuminate\Http\UploadedFile) {
                $mediaPath = FileManager::upload('sliders', $data["media_path_{$language}"]);
                $fileType = FileManager::getFileTypeFromPath($mediaPath);

                $translationData['media_path'] = $mediaPath;
                $translationData['media_type'] = $fileType;
            }

            $slider->translations()->create([
                'language' => $language,
                'title' => $data["title_{$language}"] ?? null,
                'description' => $data["description_{$language}"] ?? null,
                'button_text' => $data["button_text_{$language}"] ?? null,
                'media_path' => $translationData['media_path'] ?? null,
                'media_type' => $translationData['media_type'] ?? 'image',
            ]);
        }

        return $slider->load('translations');
    }

    public function update(int $id, array $data)
    {
        $slider = $this->getById($id);

        $slider->update([
            'is_active' => $data['is_active'] ?? $slider->is_active,
            'platform' => $data['platform'] ?? $slider->platform,
            'button_action' => $data['button_action'] ?? $slider->button_action,
        ]);

        foreach (['en', 'ar'] as $language) {
            $translationData = [];

            // Handle media file upload only if new file is provided
            if (isset($data["media_path_{$language}"]) && $data["media_path_{$language}"] instanceof \Illuminate\Http\UploadedFile) {
                $mediaPath = FileManager::upload('sliders', $data["media_path_{$language}"]);
                $fileType = FileManager::getFileTypeFromPath($mediaPath);

                $translationData['media_path'] = $mediaPath;
                $translationData['media_type'] = $fileType;
            }

            $updateData = [
                'title' => $data["title_{$language}"] ?? null,
                'description' => $data["description_{$language}"] ?? null,
                'button_text' => $data["button_text_{$language}"] ?? null,
            ];

            // Only include media data if new file was uploaded
            if (! empty($translationData)) {
                $updateData['media_path'] = $translationData['media_path'];
                $updateData['media_type'] = $translationData['media_type'];
            }

            $slider->translations()->updateOrCreate(
                ['language' => $language],
                $updateData
            );
        }

        return $slider->fresh(['translations']);
    }

    public function delete(int $id)
    {
        $slider = $this->getById($id);

        return $slider->delete();
    }

    public function updateActivation(int $id)
    {
        $slider = $this->getById($id);
        $slider->update(['is_active' => ! $slider->is_active]);

        return $slider->fresh();
    }
}
