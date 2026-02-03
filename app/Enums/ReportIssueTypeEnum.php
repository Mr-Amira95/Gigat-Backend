<?php

namespace App\Enums;

enum ReportIssueTypeEnum: string
{
    case SERVICE    = 'service';
    case PORTFOLIO  = 'portfolio';
    case FREELANCER = 'freelancer';
    case GENERAL    = 'general';

    /**
     * Return translated label
     */
    public function label(): string
    {
        return match ($this) {
            self::SERVICE    => __('service'),
            self::PORTFOLIO  => __('portfolio'),
            self::FREELANCER => __('freelancer'),
            self::GENERAL    => __('general'),
        };
    }

    /**
     * Return all enum values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Optional: return badge styling
     */
    public function badge(): string
    {
        $classes = match ($this) {
            self::SERVICE    => 'bg-blue-100 text-blue-800',
            self::PORTFOLIO  => 'bg-purple-100 text-purple-800',
            self::FREELANCER => 'bg-teal-100 text-teal-800',
            self::GENERAL    => 'bg-gray-100 text-gray-800',
        };

        return '<span class="inline-block px-3 py-1 text-xs font-medium rounded-full ' . $classes . '">'
            . $this->label() .
            '</span>';
    }
}
