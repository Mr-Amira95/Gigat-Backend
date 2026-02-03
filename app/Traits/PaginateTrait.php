<?php

namespace App\Traits;

trait PaginateTrait
{
    public function paginate($query, $perPage = null)
    {
        // $perPage = $perPage ?? request('per_page', 10);
        if (is_null($perPage) && !request()->has('page')) {
            $items = $query->get();
            $perPage = $items->count();
        }

        $perPage    = $perPage ?? request('per_page', 10);
        
        $pagination = $query->paginate($perPage);
        return [
            'data' => collect($pagination->items()),
            'meta' => [
                'current_page' => $pagination->currentPage(),
                'last_page' => $pagination->lastPage(),
                'per_page' => $pagination->perPage(),
                'total' => $pagination->total(),
            ]
        ];
    }
}
