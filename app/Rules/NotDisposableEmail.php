<?php

namespace App\Rules;

use App\Support\DisposableEmailChecker;
use Illuminate\Contracts\Validation\Rule;

class NotDisposableEmail implements Rule
{
    public function passes($attribute, $value)
    {
        return ! DisposableEmailChecker::isDisposable((string) $value);
    }

    public function message()
    {
        return 'Temporary or disposable email addresses are not allowed. Please register with a permanent work or personal email.';
    }
}
