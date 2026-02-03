<?php

namespace App\Repositories\Eloquents;

use App\Models\Profession;
use App\Repositories\Interfaces\ProfessionRepositoryInterface;

class ProfessionRepository implements ProfessionRepositoryInterface
{
    protected $model;

    public function __construct(Profession $profession)
    {
        $this->model = $profession;
    }

    public function index()
    {
        return $this->model->orderByDesc('id')->get();
    }
    public function getAllActive()
    {
        return $this->model->where('is_active', true)
            ->with('translations')
            ->get();
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function store($data)
    {
        $profession = $this->model->create([
            'is_active' => $data['is_active'] ?? true
        ]);
        foreach (['en', 'ar'] as $locale) {
            $profession->translations()->create([
                'profession_id' => $profession->id,
                'language' => $locale,
                'title' => $data["title_$locale"],
            ]);
        }

        return $profession;
    }

    public function update($id, $data)
    {
        $profession = $this->find($id);
        $profession->update([
            'is_active' => $data['is_active'] ?? $profession->is_active
        ]);

        foreach (['en', 'ar'] as $locale) {
            $profession->translations()
                ->updateOrCreate(
                    ['profession_id' => $profession->id, 'language' => $locale],
                    ['title' => $data["title_$locale"]]
                );
        }

        return $profession;
    }

    public function delete($id)
    {
        $profession = $this->find($id);
        return $profession->delete();
    }

    public function updateActivation($id)
    {
        $profession = $this->find($id);
        $profession->is_active = !$profession->is_active;
        $profession->save();
        return $profession;
    }
}
