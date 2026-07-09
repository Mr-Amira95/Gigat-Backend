<?php

namespace App\Utilities;

use App\Models\Country;
use Illuminate\Support\Facades\Cache;

class PhonePrefixResolver
{
    /**
     * A real E.164 calling code never starts with "0", and this app's own bare
     * local numbers (e.g. "0792856567" / "792856567") never exceed this length.
     * Anything unmarked (no "+"/"00") at or under this length is treated as a
     * bare local number of the default/home country rather than risking a false
     * match against an unrelated country's short calling code.
     */
    private const UNMARKED_LOCAL_MAX_DIGITS = 10;

    /** A matched calling code must leave at least this many digits behind. */
    private const MIN_LOCAL_REMAINDER = 6;

    /**
     * Split a raw phone string into ['prefix' => ..., 'phone' => ...].
     *
     * Accepts, given the default prefix "962":
     *   00962792856567, +962792856567, 962792856567, 0792856567, 792856567
     * all resolving to prefix "962", phone "792856567".
     */
    public static function resolve(?string $rawPhone): array
    {
        $defaultPrefix = (string) config('services.phone.default_prefix', '962');
        $trimmed = trim($rawPhone ?? '');
        $hasPlusMarker = $trimmed !== '' && $trimmed[0] === '+';
        $digits = preg_replace('/\D/', '', $rawPhone ?? '');

        if (str_starts_with($digits, '00')) {
            $rest = substr($digits, 2);
            return self::matchCountryCode($rest) ?? self::withDefault($defaultPrefix, $rest);
        }

        if ($hasPlusMarker) {
            return self::matchCountryCode($digits) ?? self::withDefault($defaultPrefix, $digits);
        }

        if (str_starts_with($digits, '0')) {
            return self::withDefault($defaultPrefix, $digits);
        }

        if (strlen($digits) > self::UNMARKED_LOCAL_MAX_DIGITS) {
            $match = self::matchCountryCode($digits);
            if ($match) {
                return $match;
            }
        }

        return self::withDefault($defaultPrefix, $digits);
    }

    private static function withDefault(string $prefix, string $digits): array
    {
        return ['prefix' => $prefix, 'phone' => ltrim($digits, '0')];
    }

    private static function matchCountryCode(string $digits): ?array
    {
        foreach (self::countryCodes() as $code) {
            if (str_starts_with($digits, $code) && strlen($digits) - strlen($code) >= self::MIN_LOCAL_REMAINDER) {
                return ['prefix' => $code, 'phone' => ltrim(substr($digits, strlen($code)), '0')];
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
