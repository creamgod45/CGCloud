<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

class NoIncludeHtmlRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $config = new HtmlSanitizerConfig();
        $htmlSanitizer = new HtmlSanitizer($config);
        if ($value === null) {
            $value = "";
        }
        if ($htmlSanitizer->sanitize($value) !== $value) {
            $fail('這個 :attribute 不能包含 Html。');
        }
    }
}
