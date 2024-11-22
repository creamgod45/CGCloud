<?php

namespace App\View\Components;

use App\Lib\I18N\ELanguageText;
use App\Lib\I18N\I18N;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class BooleanStringCover extends Component
{
    public string $result = "";

    public function __construct(
        public $value,
        public I18N $i18N,
    ) {
        if ($this->value === "true") {
            $this->result = $this->i18N->getLanguage(ELanguageText::BooleanStringCoverTrue);
        } else {
            $this->result = $this->i18N->getLanguage(ELanguageText::BooleanStringCoverFalse);
        }
    }

    public function render(): View
    {
        return view('components.boolean-string-cover');
    }
}
