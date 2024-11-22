@vite(['resources/css/index.css', 'resources/js/index.js'])
@use (App\Lib\I18N\ELanguageText;use App\Lib\I18N\I18N;use App\Lib\Utils\Htmlv2;use App\Lib\Utils\Utilsv2;use App\View\Components\PopoverOptions;use Illuminate\Http\Request;use Illuminate\Pagination\LengthAwarePaginator;use Illuminate\Support\Facades\Config)
@php
    /***
     * @var string[] $urlParams 當前 URL 參數
     * @var array $moreParams 更多參數
     * @var I18N $i18N I18N 本地化語言系統
     * @var Request $request 請求
     * @var string $fingerprint 客戶端指紋
     * @var string $theme 主題

     * @var \App\Models\ShopConfig[] $styleConfig 系統設定
     */
    $menu=true;
    $footer=true;

    $inventorys = $moreParams[0]['inventorys'];
    $search = $moreParams[0]['search'];
    $maxPrice = $minPrice = 0;
    if($inventorys instanceof LengthAwarePaginator){
        $maxPrice = number_format((float)$moreParams[0]['maxPrice'], 2, ".", "");
        $minPrice = number_format((float)$moreParams[0]['minPrice'], 2, ".", "");
    }
@endphp
@extends('layouts.default')
@section('title', Config::get("app.description")." | ".Config::get('app.name'))
@section('head')
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ Config::get("app.name") }}">
    <meta name="twitter:site" content="{{ "@".Config::get("app.url") }}">
    <meta name="twitter:description" content="{{ Config::get("app.description") }}">
    <meta name="twitter:image" content="{{ asset("favicon.ico") }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ Config::get("app.name") }}">
    <meta property="og:url" content="{{ Config::get("app.url") }}">
    <meta property="og:image" content="{{ asset("favicon.ico") }}">
    <meta property="og:image:width" content="128">
    <meta property="og:image:height" content="128">
    <meta property="og:description" content="{{ Config::get("app.description") }}">
@endsection
@section('content')
    <x-scroll-indicator indicator-target="body"></x-scroll-indicator>
    <main>
        <div class="container1">
            @php
                $popover = "PW_".\Illuminate\Support\Str::random();
            @endphp
            <x-popover-windows class="shop-popover !hidden"
                               popover-title="預覽商品" :id="$popover"
                               :popover-options="new PopoverOptions()">
                <div class="shop-popover-placeholder placeholder placeholder-full-wh">
                    <div class="shop-popover-loader" role="status">
                        <svg aria-hidden="true"
                             class="inline w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-red-600"
                             viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                fill="currentColor"/>
                            <path
                                d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                fill="currentFill"/>
                        </svg>
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <iframe class="shop-iframe"></iframe>
            </x-popover-windows>
            <x-FilterBar :maxPrice="$maxPrice" :minPrice="$minPrice"></x-FilterBar>
            <div class="shop-list-frame">
                @if($search !== null && $search !== "")
                    <div class="search-text">
                        搜尋結果: {{$search}} 搜尋數量: {{ $inventorys->total() }}
                    </div>
                @endif
                @if($inventorys->total() === 0)
                <h1>無搜尋結果</h1>
                @else
                <x-ShopItem2 :popoverid="$popover" :i18-n="$i18N" :inventorys="$inventorys"></x-ShopItem2>
                @endif
            </div>
        </div>
    </main>
@endsection
