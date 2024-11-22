<?php

namespace App\View\Components;

use App\Models\Inventory;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ShopItemPage extends Component
{
    public function __construct(
        public Inventory $shopItem,
        public ?ShopItemPageOption $shopItemOption = null,
    ) {
        if ($this->shopItemOption === null) {
            $this->shopItemOption = new ShopItemPageOption();
        }
    }

    public function render(): View
    {
        return view('components.shop-item-page');
    }
}
