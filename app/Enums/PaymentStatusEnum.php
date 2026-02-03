<?php

namespace App\Enums;

enum PaymentStatusEnum: string
{

    case PAID = 'paid';
    case UNPAID = 'unpaid';

    public function label(): string
    {
        return match ($this) {
            self::PAID => __('paid'),
            self::UNPAID => __('unpaid'),

        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
    public function badge(): string
    {
        $classes = match ($this) {
            self::PAID => 'bg-green-100 text-green-800',
            self::UNPAID => 'bg-danger text-white',
        };

        return '<span class="inline-block px-3 py-1 text-xs font-medium rounded-full ' . $classes . '">'
            . $this->label() .
            '</span>';
    }
}
