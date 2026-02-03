<?php

namespace App\Enums;

enum ReportIssueStatusEnum: string
{
    case PENDING   = 'pending';
    case RESOLVED  = 'resolved';
    case CANCELLED = 'cancelled';

    /**
     * Return translated label
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING   => __('pending'),
            self::RESOLVED  => __('resolved'),
            self::CANCELLED => __('cancelled'),
        };
    }

    /**
     * Return array of all values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Return a styled badge
     */
    public function badge(): string
    {
        $classes = match ($this) {
            self::PENDING   => 'bg-yellow-100 text-yellow-800',
            self::RESOLVED  => 'bg-green-100 text-green-800',
            self::CANCELLED => 'bg-red-100 text-red-800',
        };

        return '<span class="inline-block px-3 py-1 text-xs font-medium rounded-full ' . $classes . '">'
            . $this->label() .
            '</span>';
    }
}
