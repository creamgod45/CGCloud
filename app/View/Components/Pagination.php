<?php

namespace App\View\Components;

use App\Lib\I18N\I18N;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\Component;

class Pagination extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public I18N $i18N,
        public LengthAwarePaginator $elements,
        public int $nopaginationframe,
        public bool $headerPageAction,
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.pagination');
    }
}
