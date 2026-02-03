<?php

namespace App\Observers;

use App\Models\Category;

class CategoryObserver
{

    public function updating(Category $category)
    {
        if ($category->isDirty('is_active')) {
            $newStatus = $category->is_active;

            $category->subCategories()->update(['is_active' => $newStatus]);
        }
    }
    public function deleting(Category $category)
    {
        $category->faqs()->delete();
    }
}
