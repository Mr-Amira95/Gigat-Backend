<?php

namespace App\Enums;

enum TicketStatusEnum: string
{
    case OPEN = 'open';
    case PENDING = 'pending';
    case RESOLVED = 'resolved';
    case CLOSED = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::OPEN => __('open'),
            self::PENDING => __('pending'),
            self::RESOLVED => __('resolved'),
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
            self::OPEN => 'bg-primary text-white',
            self::PENDING => 'bg-yellow-100 text-yellow-800',
            self::RESOLVED => 'bg-green-100 text-green-800',
            self::CLOSED => 'bg-gray-200 text-gray-700',
        };

        return '<span class="inline-block px-3 py-1 text-xs font-medium rounded-full ' . $classes . '">'
            . $this->label() .
            '</span>';
    }
}
