<?php

namespace App\Enums;

enum FreelancerStatusEnum: string
{
    case VERIFIED = 'verified';
    case UNVERIFIED = 'unverified';

    public function label(): string
    {
        return match ($this) {
            self::VERIFIED => __('verified'),
            self::UNVERIFIED => __('unverified'),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
