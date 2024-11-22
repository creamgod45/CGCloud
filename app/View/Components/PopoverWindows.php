<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\View\Component;

class PopoverWindows extends Component
{
    public string $id;

    public function __construct(
        public string $popoverTitle,
        public PopoverOptions $popoverOptions,
        $id = null,
    ) {
        if ($id !== null) {
            $this->id = $id;
        } else {
            $this->id = "P" . Str::random(20);
        }
    }

    public function render(): View
    {
        return view('components.popover-windows');
    }
}
