<?php

namespace App\Utilities;

use App\Models\Country;
use Illuminate\Support\Facades\Cache;

class PhonePrefixResolver
{
    /**
     * A real E.164 calling code never starts with "0". Anything unmarked
     * (no "+"/"00") at or under this length is short enough to plausibly be a
     * bare local number, so no country-code match is attempted for it.
     */
    private const UNMARKED_LOCAL_MAX_DIGITS = 10;

    /** A matched calling code must leave at least this many digits behind. */
    private const MIN_LOCAL_REMAINDER = 6;

    /**
     * Split a raw phone string into ['prefix' => ..., 'phone' => ...].
     *
     * 'prefix' is null when the number has no determinable country code (a
     * bare local number, e.g. "0792856567" / "792856567") — callers should
     * then match on the local number alone, across all prefixes.
     *
     * Otherwise, e.g. 00962792856567, +962792856567, or 962792856567 (11+
     * digits) all resolve to prefix "+962", phone "792856567".
     */
    public static function resolve(?string $rawPhone): array
    {
        $trimmed = trim($rawPhone ?? '');
        $hasPlusMarker = $trimmed !== '' && $trimmed[0] === '+';
        $digits = preg_replace('/\D/', '', $rawPhone ?? '');

        if ($hasPlusMarker) {
            return self::matchCountryCode($digits) ?? self::withoutPrefix($digits);
        }

        if (str_starts_with($digits, '00')) {
            $rest = substr($digits, 2);
            return self::matchCountryCode($rest) ?? self::withoutPrefix($rest);
        }

        if (strlen($digits) > self::UNMARKED_LOCAL_MAX_DIGITS) {
            return self::matchCountryCode($digits) ?? self::withoutPrefix($digits);
        }

        return self::withoutPrefix($digits);
    }

    private static function withoutPrefix(string $digits): array
    {
        return ['prefix' => null, 'phone' => ltrim($digits, '0')];
    }

    private static function matchCountryCode(string $digits): ?array
    {
        foreach (self::countryCodes() as $code) {
            if (str_starts_with($digits, $code) && strlen($digits) - strlen($code) >= self::MIN_LOCAL_REMAINDER) {
                return ['prefix' => '+' . $code, 'phone' => ltrim(substr($digits, strlen($code)), '0')];
            }
        }

        return null;
    }

    /** All known calling codes, digits only, longest first so e.g. "962" wins over a shorter false match. */
    private static function countryCodes(): array
    {
        return Cache::remember('country_phone_codes', now()->addHours(6), function () {
            return Country::pluck('phone_code')
                ->map(fn ($code) => preg_replace('/\D/', '', $code ?? ''))
                ->filter()
                ->unique()
                ->sortByDesc(fn ($code) => strlen($code))
                ->values()
                ->all();
        });
    }
}
