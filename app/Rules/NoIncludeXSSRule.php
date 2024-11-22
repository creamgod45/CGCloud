<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use voku\helper\AntiXSS;

class NoIncludeXSSRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $antiXss = new AntiXSS();
        if ($value === null) {
            $value = "";
        }
        $antiXss->xss_clean($value);

        // 如果去除 HTML 标签后，字符串不相同，则表示输入包含 HTML 标签
        if ($antiXss->isXssFound()) {
            $fail('這個 :attribute 不能包含 XSS。');
        }
    }
}
