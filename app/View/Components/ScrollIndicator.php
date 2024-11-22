<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ScrollIndicator extends Component
{
    public function __construct(
        public string $indicatorTarget = 'body'
    )
    {
    }

    public function render()
    {
        return view('components.scroll-indicator');
    }
}
