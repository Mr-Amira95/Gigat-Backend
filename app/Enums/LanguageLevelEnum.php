<?php

namespace App\Enums;

enum LanguageLevelEnum: string
{
    case BEGINNER = 'beginner';
    case INTERMEDIATE = 'intermediate';
    case ADVANCED = 'advanced';
    case NATIVE = 'native';
    case FLUENT = 'fluent';

    public function label(): string
    {
        return match ($this) {
            self::BEGINNER => __('beginner'),
            self::INTERMEDIATE => __('intermediate'),
            self::ADVANCED => __('advanced'),
            self::NATIVE => __('native'),
            self::FLUENT => __('fluent'),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
