<?php

namespace App\Rules;

use App\Models\Country;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Cache;

/**
 * Phone numbers must be international-style and begin with an ITU calling code
 * that belongs to an African member state row in `country` (same list as the
 * account country dropdown: `national = national`, non-empty `phonecode`).
 */
class AfricanMemberStateInternationalPhone implements Rule
{
    public const CACHE_KEY = 'african_member_state_calling_codes_v1';

    public function passes($attribute, $value)
    {
        $raw = trim((string) ($value ?? ''));
        if ($raw === '') {
            return true;
        }

        $digits = preg_replace('/\D+/', '', $raw);
        if ($digits === '') {
            return false;
        }

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        if (strlen($digits) < 8) {
            return false;
        }

        foreach (self::callingCodesLongestFirst() as $prefix) {
            if ($prefix !== '' && str_starts_with($digits, $prefix)) {
                return true;
            }
        }

        return false;
    }

    public function message()
    {
        return 'Enter your phone number in international format using an African member state country code (e.g. +251 11 551 7700), matching the codes in our country list.';
    }

    /**
     * @return list<string>
     */
    public static function callingCodesLongestFirst(): array
    {
        return Cache::remember(self::CACHE_KEY, 3600, function () {
            $codes = Country::query()
                ->where('national', 'national')
                ->whereNotNull('phonecode')
                ->where('phonecode', '!=', '')
                ->pluck('phonecode')
                ->map(function ($c) {
                    return preg_replace('/\D+/', '', (string) $c);
                })
                ->filter()
                ->unique()
                ->values()
                ->all();

            usort($codes, function ($a, $b) {
                return strlen((string) $b) <=> strlen((string) $a);
            });

            return $codes;
        });
    }
}
