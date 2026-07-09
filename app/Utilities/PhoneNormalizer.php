<?php

namespace App\Utilities;

class PhoneNormalizer
{
    /**
     * Normalize a phone number to the bare local number stored in the database.
     *
     * Accepts phone numbers submitted in any of the following forms (given prefix "962"):
     *   00962792856567, +962792856567, 962792856567, 0792856567, 792856567
     * and returns "792856567" for all of them.
     */
    public static function normalize(?string $phone, ?string $prefix): string
    {
        $digits = preg_replace('/\D/', '', $phone ?? '');
        $prefix = preg_replace('/\D/', '', $prefix ?? '');

        if ($prefix !== '') {
            if (str_starts_with($digits, '00' . $prefix)) {
                $digits = substr($digits, strlen('00' . $prefix));
            } elseif (str_starts_with($digits, $prefix)) {
                $digits = substr($digits, strlen($prefix));
            }
        }

        return ltrim($digits, '0');
    }
}
