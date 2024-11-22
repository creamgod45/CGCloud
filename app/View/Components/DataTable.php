<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DataTable extends Component
{
    public function __construct(
        public TableOption $tableOption,
    ) {

    }

    public function render(): View
    {
        return view('components.DataTable');
    }
}
