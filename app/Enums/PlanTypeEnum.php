<?php

namespace App\Enums;

enum PlanTypeEnum: string
{
    case BASIC = 'basic';
    case STANDARD = 'standard';
    case PREMIUM = 'premium';

    public function label(): string
    {
        return match ($this) {
            self::BASIC => __('basic'),
            self::STANDARD => __('standard'),
            self::PREMIUM => __('premium'),
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