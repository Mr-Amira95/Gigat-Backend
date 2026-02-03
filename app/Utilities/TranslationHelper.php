<?php

namespace App\Utilities;

use Illuminate\Support\Facades\App;

class TranslationHelper
{
    public static function getTranslation($model, $column)
    {
        return $model->translations
            ->where('language', App::getLocale())
            ->value($column) ?? $model->translations->first()?->$column;
    }
}
