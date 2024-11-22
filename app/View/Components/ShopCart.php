<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ShopCart extends Component
{
    public function render(): View
    {
        return view('components.shop-cart');
    }
}
