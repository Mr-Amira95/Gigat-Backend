<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ConvertNumbers implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = $this->convertToEnglish($value);

        
        if (!ctype_digit($value)) {
            $fail(__('The :attribute must be a number.'));
        }
    }

    /**
     * Convert Arabic/Persian numbers to English and remove non-numeric characters.
     */
    private function convertToEnglish($value): string
    {
        if (!$value) {
            return '';
        }

        
        $value = strtr($value, [
            '۰' => '0',
            '۱' => '1',
            '۲' => '2',
            '۳' => '3',
            '۴' => '4',
            '۵' => '5',
            '۶' => '6',
            '۷' => '7',
            '۸' => '8',
            '۹' => '9',
        ]);

        
        return preg_replace('/\D/', '', $value);
    }
}
