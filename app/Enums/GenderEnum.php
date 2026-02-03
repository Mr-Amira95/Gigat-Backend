<?php

namespace App\Enums;

enum GenderEnum: string
{
    case MALE = 'male';
    case FEMALE = 'female';
    case PREFER_NOT_SAY = 'prefer_not_say';


    public function label(): string
    {
        return match ($this) {
            self::MALE => __('male'),
            self::FEMALE => __('female'),
            self::PREFER_NOT_SAY => __('prefer_not_say'),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
