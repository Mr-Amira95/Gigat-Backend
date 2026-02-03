<?php

namespace App\Enums;

enum QuotationStatusEnum: string
{

    case OPEN = 'open';
    case CLOSED = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::OPEN => __('open'),
            self::CLOSED => __('closed'),

        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
    public function badge(): string
    {
        $classes = match ($this) {
            self::OPEN => 'bg-green-100 text-green-800',
            self::CLOSED => 'bg-danger text-white',
        };

        return '<span class="inline-block px-3 py-1 text-xs font-medium rounded-full ' . $classes . '">'
            . $this->label() .
            '</span>';
    }
}
