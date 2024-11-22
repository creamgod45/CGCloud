<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class JsonString implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //
        //$value = Str::replace("\\", "", $value);
        $json_decode = json_decode($value);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $fail('這個 :attribute 不是正確的數值。');
        }
    }
}
