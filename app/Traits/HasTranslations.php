<?php

namespace App\Traits;

use Illuminate\Support\Facades\App;

trait HasTranslations
{
    public function translation()
    {
        return $this->hasOne($this->getTranslationModel())
            ->where('language', App::getLocale())
            ->withDefault(function () {
                return $this->translations()->first();
            });
    }

    public function translations()
    {
        return $this->hasMany($this->getTranslationModel());
    }

    protected function getTranslationModel()
    {
        return str_replace('Models\\', 'Models\\', get_class($this)) . 'Translation';
    }
}
