<?php

namespace App\View\Components;

use App\Lib\I18N\I18N;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\Component;

class ShopItem extends Component
{

    /**
     * @var string
     */
    public string $popoverid;

    /**
     * @param LengthAwarePaginator $inventorys
     * @param I18N                 $i18N
     * @param string|null          $popoverid
     */
    public function __construct(
        public LengthAwarePaginator $inventorys,
        public I18N $i18N,
        $popoverid = null,
    ) {
        if ($popoverid !== null) {
            $this->popoverid = $popoverid;
        } else {
            $this->popoverid = "";
        }
    }

    public function render(): View
    {
        return view('components.ShopItem');
    }
}
