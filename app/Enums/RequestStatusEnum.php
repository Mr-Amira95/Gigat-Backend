<?php

namespace App\Enums;

enum RequestStatusEnum: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CONFIRMED = 'confirmed';
    case CANCELLED = 'cancelled';
    case APPROVED    = 'approved';

    // public function label(): string
    // {
    //     return match ($this) {
    //         self::PENDING => __('pending'),
    //         self::CONFIRMED => __('confirmed'),
    //         self::IN_PROGRESS => __('in_progress'),
    //         self::CANCELLED => __('cancelled'),
    //         self::COMPLETED => __('completed'),
    //     };
    // }
    public function label($locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return match ($this) {
            self::PENDING     => __('pending', locale: $locale),
            self::CONFIRMED   => __('confirmed', locale: $locale),
            self::IN_PROGRESS => __('in_progress', locale: $locale),
            self::CANCELLED   => __('cancelled', locale: $locale),
            self::COMPLETED   => __('completed', locale: $locale),
            self::APPROVED    => __('approved', locale: $locale),
        };
    }


    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
    public function badge(): string
    {
        $classes = match ($this) {
            self::PENDING => 'bg-yellow-100 text-yellow-800',
            self::IN_PROGRESS => 'bg-primary text-white',
            self::COMPLETED => 'bg-green-100 text-green-800',
            self::CONFIRMED => 'bg-gray-200 text-gray-700',
            self::CANCELLED => 'bg-danger text-white',
            self::APPROVED    => 'bg-blue-100 text-blue-800',
        };

        return '<span class="inline-block px-3 py-1 text-xs font-medium rounded-full ' . $classes . '">'
            . $this->label() .
            '</span>';
    }
}
