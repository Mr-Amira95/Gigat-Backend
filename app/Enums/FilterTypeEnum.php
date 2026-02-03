<?php

namespace App\Enums;

enum FilterTypeEnum: string
{
    case CHECKBOX = 'checkbox';
    case DROPDOWN = 'dropdown';
    case DROPDOWN_MULTIPLE = 'dropdown_multiple';
    case RANGE = 'range';
    case NUMBER = 'number';
    case RATING = 'rating';

    public function label(): string
    {
        return match ($this) {
            self::CHECKBOX => __('checkbox'),
            self::DROPDOWN => __('dropdown'),
            self::DROPDOWN_MULTIPLE => __('dropdown_multiple'),
            self::RANGE => __('range'),
            self::NUMBER => __('number'),
            self::RATING => __('rating'),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return collect(self::cases())->map(function ($type) {
            return [
                'value' => $type->value,
                'label' => $type->label(),
            ];
        })->toArray();
    }
}
