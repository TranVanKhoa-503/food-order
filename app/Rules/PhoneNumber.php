<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class PhoneNumber implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * Validates Vietnamese mobile phone numbers:
     * - Starting with '0' followed by 9 digits (10 digits total: 0xxxxxxxxx)
     * - Or international format starting with '+84' followed by 9 digits (+84xxxxxxxxx)
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! preg_match('/^(0|\+84)[0-9]{9}$/', trim($value))) {
            $fail('Số điện thoại không hợp lệ. Vui lòng nhập đúng định dạng 0xxxxxxxxx hoặc +84xxxxxxxxx.');
        }
    }
}
