@use (App\Lib\I18N\ELanguageText;use App\Lib\I18N\I18N;use App\Lib\Utils\Htmlv2;use App\Lib\Utils\Utilsv2;use App\Models\Inventory;use App\View\Components\ShopItemPageOption;use Illuminate\Http\Request;use Illuminate\Support\Facades\Config;use App\Lib\Utils\RouteNameField)
@php
    /***
     * @var string[] $urlParams 當前 URL 參數
     * @var array $moreParams 更多參數
     * @var I18N $i18N I18N 本地化語言系統
     * @var Request $request 請求
     * @var string $fingerprint 客戶端指紋
     * @var string $theme 主題

     * @var \App\Models\ShopConfig[] $styleConfig 系統設定
     * @var Inventory $shopItem
     */
    $menu=false;
    $footer=false;

    $shopItem = $moreParams[0]['shopItem'];
    $minPrice = $moreParams[0]['minPrice'];
    $maxPrice = $moreParams[0]['maxPrice'];
    $asset = $moreParams[0]['asset'];
    //dump($shopItem);
@endphp
@if($asset === true || $asset === "true")
    @vite(['resources/css/index.css', 'resources/js/index.js'])
@endif
@php
    $shopItemPageoption = new ShopItemPageOption("normal", topSpaceing: "20", stickysize: "", maxPrice: $maxPrice,
            minPrice: $minPrice);
@endphp
@include('layouts.style')
<x-scroll-indicator indicator-target="body"></x-scroll-indicator>
<x-shop-item-page :shop-item-option="$shopItemPageoption" :shop-item="$shopItem"></x-shop-item-page>
