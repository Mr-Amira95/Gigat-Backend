<?php

namespace App\Repositories\Eloquents;

use App\Models\Release;
use App\Repositories\Interfaces\ReleaseRepositoryInterface;

class ReleaseRepository implements ReleaseRepositoryInterface
{
    protected $model;

    public function __construct(Release $release)
    {
        $this->model = $release;
    }

    public function all()
    {
        return $this->model->active()->latest()->get();
    }

    public function find($id)
    {
        return $this->model->active()->find($id);
    }

    public function latest()
    {
        return $this->model->active()->latest()->first();
    }
    public function allForAdmin()
    {
        return $this->model->latest()->get();
    }

    public function findForAdmin($id)
    {
        return $this->model->find($id);
    }

    // public function create($data)
    // {
    //     return $this->model->create($data);
    // }
    public function create($data)
    {
        $release = $this->model->create([
            'android_version' => $data['android_version'] ?? null,
            'ios_version'     => $data['ios_version'] ?? null,
            'web_version'     => $data['web_version'] ?? null,
            'is_required'     => isset($data['is_required']) ? (bool)$data['is_required'] : false,
            'is_active'       => isset($data['is_active']) ? (bool)$data['is_active'] : true,
        ]);

        foreach (['en', 'ar'] as $locale) {
            $release->translations()->create([
                'language'      => $locale,
                'release_note'  => $data["release_note_$locale"],
            ]);
        }

        return $release;
    }

    // public function update($id, $data)
    // {
    //     $release = $this->model->findOrFail($id);
    //     return $release->update($data);
    // }
    public function update($id, $data)
    {
        $release = $this->model->findOrFail($id);

        $release->update([
            'android_version' => $data['android_version'] ?? $release->android_version,
            'ios_version'     => $data['ios_version'] ?? $release->ios_version,
            'web_version'     => $data['web_version'] ?? $release->web_version,
            'is_required'     => isset($data['is_required']) ? (bool)$data['is_required'] : $release->is_required,
            'is_active'       => isset($data['is_active']) ? (bool)$data['is_active'] : $release->is_active,
        ]);

        foreach (['en', 'ar'] as $locale) {
            $release->translations()
                ->where('language', $locale)
                ->update([
                    'release_note' => $data["release_note_$locale"],
                ]);
        }

        return $release;
    }

    public function delete($id)
    {
        $release = $this->model->findOrFail($id);
        return (bool) $release->delete();
    }

    public function updateActivation($id)
    {
        $release = $this->model->findOrFail($id);

        $release->is_active = !$release->is_active;
        $release->save();

        return $release;
    }
}
