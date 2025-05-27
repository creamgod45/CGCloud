<?php

namespace App\View\Components;

use App\Lib\I18N\I18N;
use App\Models\ShareTable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;

class PanelFieldCardList extends Component
{
    public function __construct(
        /**
         * @var \Illuminate\Pagination\LengthAwarePaginator $shareTables
         */
        public mixed $shareTables,
        public string $popoverid,
        public I18N $i18N,
    )
    {
    }

    public function render(): View
    {
        return view('components.panel-field-card-list');
    }
}
