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
    $menu=true;
    $footer=true;

    $shopItem = $moreParams[0]['shopItem'];
    $minPrice = $moreParams[0]['minPrice'];
    $maxPrice = $moreParams[0]['maxPrice'];
    //dump($shopItem);
    $image_url = $shopItem->image_url;
@endphp
@extends('layouts.default')
@section('title', $shopItem->name." | ".Config::get('app.name'))
@section('head')
    @vite(['resources/css/index.css', 'resources/js/index.js'])
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ Config::get("app.name") }} | {{ $shopItem->name }}">
    <meta name="twitter:site" content="{{ "@".route(RouteNameField::PageShopItem->value,['id' => $shopItem->id]) }}">
    <meta name="twitter:description" content="{{ $shopItem->description }}">
    <meta name="twitter:image" content="{{ $image_url->getImage(0)->getUri() }}">
    <meta property="og:type" content="product">
    <meta property="og:title" content="{{ Config::get("app.name") }}">
    <meta property="og:url" content="{{ route(RouteNameField::PageShopItem->value, ['id' => $shopItem->id]) }}">
    <meta property="og:image" content="{{ $image_url->getImage(0)->getUri() }}">
    <meta property="og:image:width" content="128">
    <meta property="og:image:height" content="128">
    <meta property="og:description" content="{{ $shopItem->description }}">
    <meta property="product:plural_title" content="{{ $shopItem->name }}">
    <meta property="product:price.amount" content="{{ $shopItem->price }}">
    <meta property="product:price.currency" content="TWD">
    <script type="application/ld+json">
        {
            "@context": "http://schema.org/",
            "@type": "Product",
            "name": "{{ Config::get("app.name") }} | {{ $shopItem->name }}",
            "image": "{{ $image_url->getImage(0)->getUri() }}",
            "description": "{{ $shopItem->description }}",
            "gtin13": "{{ $shopItem->GTIN }}",
            "mpn": "{{ $shopItem->MPN }}",
            "sku": "{{ $shopItem->SKU }}",
            "brand": {
                "@type": "Brand",
                "name": "{{ $shopItem->brand }}"
            },
            "offers": {
                "@type": "Offer",
                "priceCurrency": "TWD",
                "price": "{{ $shopItem->price }}",
                "url": "{{ route(RouteNameField::PageShopItem->value, ['id' => $shopItem->id]) }}",
                "itemCondition": "https://schema.org/NewCondition"
            }
        }
    </script>
@endsection
@section('content')
    @php
        $shopItemPageoption = new ShopItemPageOption("normal",topSpaceing: "100", stickysize: "988", maxPrice: $maxPrice,
            minPrice: $minPrice);
    @endphp
    <x-scroll-indicator class="!bottom-[14px]" indicator-target="html"></x-scroll-indicator>
    <input type="hidden" id="shopitemid" value="{{ $shopItem->id }}">
    <x-shop-item-page :shop-item-option="$shopItemPageoption" :shop-item="$shopItem"></x-shop-item-page>
@endsection
