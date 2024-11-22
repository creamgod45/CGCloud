<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Str;

class Popover extends Component
{
    public string $id;

    public function __construct(
        public string $popoverBtnMessage,
        public string $btnClassList,
        public string $popoverTitle,
        public PopoverOptions $popoverOptions,
    ) {
        $this->id = "P" . Str::random(20);
    }

    public function render(): View
    {
        return view('components.popover');
    }
}
