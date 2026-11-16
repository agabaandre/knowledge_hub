<?php

namespace App\Rules;

use App\Support\SafeUrlFetcher;
use Illuminate\Contracts\Validation\Rule;

/**
 * URL must be http(s) and resolve to a public address — rejects
 * http://127.0.0.1/, internal hosts, and cloud metadata endpoints.
 */
class SafePublicUrl implements Rule
{
    public function passes($attribute, $value)
    {
        if ($value === null || $value === '') {
            return true; // pair with 'required' where a value is mandatory
        }

        return SafeUrlFetcher::validate((string) $value) !== null;
    }

    public function message()
    {
        return 'The :attribute must be a public http(s) URL.';
    }
}
