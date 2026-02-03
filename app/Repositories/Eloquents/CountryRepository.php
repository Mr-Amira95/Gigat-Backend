<?php

namespace App\Repositories\Eloquents;

use App\Models\Country;
use App\Traits\PaginateTrait;
use App\Repositories\Interfaces\CountryRepositoryInterface;

class CountryRepository implements CountryRepositoryInterface
{
    use PaginateTrait;
    protected $model;

    public function __construct(Country $country)
    {
        $this->model = $country;
    }

    public function index()
    {
        $locale = app()->getLocale();
        $column = $locale === 'ar' ? 'title_ar' : 'title_en';
        return $this->model->orderBy($column, 'asc')->get();
    }

    public function getAllActive($perPage = null, $search = null)
    {
        $locale = app()->getLocale();
        $column = $locale === 'ar' ? 'title_ar' : 'title_en';

        $query = $this->model->where('is_active', true)->orderBy($column, 'asc');

        if ($search) {
            $query->where($column, 'LIKE', "%{$search}%");
        }

        return $query->get();
    }


    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function store($data)
    {
        $locale = app()->getLocale();
        $country = $this->model->create([
            'flag'        => $data['flag'],
            "title_{$locale}" => $data['title'],
        ]);

        return $country;
    }

    public function update($id, $data)
    {
        $country = $this->find($id);

        $locale = app()->getLocale();
        $updateData = [
            "title_{$locale}" => $data['title'],
            'flag'             => $data['flag'],
        ];

        $country->update($updateData);

        return $country;
    }

    public function delete($id)
    {
        $country = $this->find($id);
        return $country->delete();
    }

    public function updateActivation($id)
    {
        $country = $this->find($id);
        $country->is_active = !$country->is_active;
        $country->save();
        return $country;
    }
}
