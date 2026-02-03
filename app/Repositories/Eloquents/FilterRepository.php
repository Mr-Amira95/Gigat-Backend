<?php

namespace App\Repositories\Eloquents;

use App\Models\Filter;
use App\Repositories\Interfaces\FilterRepositoryInterface;
use Illuminate\Support\Facades\DB;

class FilterRepository implements FilterRepositoryInterface
{
    protected $model;
    public function __construct(Filter $filter)
    {
        $this->model = $filter;
    }
    public function index($categoryId = null)
    {
        $query = $this->model->with(['category', 'options']);
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        return $query->orderBy('id', 'desc')->get();
    }
    public function store($data)
    {
        try {
            DB::beginTransaction();

            $filters = []; 

            foreach ($data['category_id'] as $categoryId) { 
                $filter = $this->model->create([
                    'category_id' => $categoryId,
                    'type' => $data['type'],
                    'min_value' => $data['min_value'] ?? null, 
                    'max_value' => $data['max_value'] ?? null  
                ]);

                
               
                    foreach (['en', 'ar'] as $language) {
                        $filter->translations()->create([
                            'language' => $language,
                            'title' => $data['translations'][$language]['title']
                        ]);
                    }
                if ($data['type'] === 'dropdown' || $data['type'] === 'dropdown_multiple') {
                    if (isset($data['options']) && is_array($data['options'])) {
                        foreach ($data['options'] as $option) {
                            if (!isset($option['translations'])) {
                                continue;
                            }


                            $filterOption = $filter->options()->create();

                            foreach (['en', 'ar'] as $language) {
                                if (isset($option['translations'][$language]['title'])) {
                                    $filterOption->translations()->create([
                                        'language' => $language,
                                        'title' => $option['translations'][$language]['title']
                                    ]);
                                }
                            }
                        }
                    }
                }

                $filters[] = $filter; 
            }

            DB::commit();
            return $filters; 
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    public function update($id, $data)
    {
        try {
            DB::beginTransaction();
            $filter = $this->model->findOrFail($id);
            $filter->update([
                'category_id' => $data['category_id'],
                'type' => $data['type']
            ]);
            foreach (['en', 'ar'] as $language) {
                $filter->translations()->updateOrCreate(
                    ['language' => $language],
                    ['title' => $data['translations'][$language]['title']]
                );
            }
            if (isset($data['options']) && is_array($data['options'])) {
                $existingOptionIds = $filter->options()->pluck('id')->toArray();
                $updatedOptionIds = [];
                foreach ($data['options'] as $option) {
                    if (!isset($option['translations'])) {
                        continue;
                    }
                    if (isset($option['id'])) {
                        $filterOption = $filter->options()->findOrFail($option['id']);
                        $updatedOptionIds[] = $option['id'];
                    } else {
                        $filterOption = $filter->options()->create();
                    }
                    foreach (['en', 'ar'] as $language) {
                        if (isset($option['translations'][$language]['title'])) {
                            $filterOption->translations()->updateOrCreate(
                                ['language' => $language],
                                ['title' => $option['translations'][$language]['title']]
                            );
                        }
                    }
                }
                $optionsToDelete = array_diff($existingOptionIds, $updatedOptionIds);
                if (!empty($optionsToDelete)) {
                    $filter->options()->whereIn('id', $optionsToDelete)->delete();
                }
            }
            DB::commit();
            return $filter;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public function delete($id)
    {
        $filter = $this->model->findOrFail($id);
        return $filter->delete();
    }
    public function find($id)
    {
        return $this->model->with('options')->findOrFail($id);
    }
    public function getFiltersByCategoryId($categoryId)
    {
        return $this->model->with('options')->where('category_id', $categoryId)->get();
    }
}
