<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;

class RegexPassword implements Rule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function passes($attribute, $value)
    {
        // Enforce password strength:
        // - At least one uppercase letter
        // - At least one lowercase letter
        // - At least one number
        // - At least one special character
        // - Minimum 8 characters
        $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
        return preg_match($pattern, $value);
    }

    /**
     * Get the error message for an invalid password.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be at least 8 characters, with an uppercase letter, a lowercase letter, a number, and a special character.';
    }
}
