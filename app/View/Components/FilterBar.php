<?php

namespace App\View\Components;

use Illuminate\View\Component;

class FilterBar extends Component
{
    public function __construct(
        public $maxPrice,
        public $minPrice,
    ) {
    }

    public function render()
    {
        return view('components.FilterBar');
    }
}
