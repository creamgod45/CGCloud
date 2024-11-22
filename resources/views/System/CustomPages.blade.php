@use (App\Lib\I18N\ELanguageText;use App\Lib\I18N\I18N;use App\Lib\Inventory\EInventoryStatus;use App\Lib\Utils\Htmlv2;use App\Lib\Utils\Utilsv2;use Illuminate\Http\Request;use Illuminate\Support\Facades\Config;use App\Lib\Utils\RouteNameField;use Nette\Utils\Json)
@php
    /***
     * @var string[] $urlParams 當前 URL 參數
     * @var array $moreParams 更多參數
     * @var I18N $i18N I18N 本地化語言系統
     * @var Request $request 請求
     * @var string $fingerprint 客戶端指紋
     * @var string $theme 主題

     * @var \App\Models\ShopConfig[] $styleConfig 系統設定
     * @var \App\Models\CustomizePage[] $customizePage
     */
    $menu=true;
    $footer=true;
    $customizePage = null;
    if(!empty($moreParams)){
        if(isset($moreParams[0]['customizePage'])){
            $customizePage = $moreParams[0]['customizePage'];
        }
    }
@endphp
@extends('layouts.default')
@section('title', "所有自訂頁面 | ".Config::get('app.name'))
@section('head')
    @vite(['resources/css/index.css', 'resources/js/index.js'])
@endsection
@section('content')
    <x-scroll-indicator class="!bottom-[14px]" indicator-target="html"></x-scroll-indicator>
    <main>
        @php
            $popover = "PW_".\Illuminate\Support\Str::random();
        @endphp
        <div class="container6">
            <x-popover-windows class="shop-popover !hidden"
                               popover-title="預覽自訂頁面" :id="$popover"
                               :popover-options="new \App\View\Components\PopoverOptions()">
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
                <iframe class="custom-page-iframe"></iframe>
            </x-popover-windows>
            <div class="list-item-frame">
                <x-pagination :header-page-action="true" :elements="$customizePage" :nopaginationframe="0">
                    @foreach($customizePage as $item)
                        <div class="list-item">
                            <div class="namespace">{{ $item->namespace }}</div>
                            <div class="content-flex">
                                <div class="html">{{ $item->html }}</div>
                                <div class="btn btn-ripple btn-color7 ct" data-fn="popover2" data-source="{{ $item->id }}" data-target="#{{ $popover }}"><i class="fa-solid fa-pen-to-square"></i> 編輯頁面</div>
                            </div>
                            <div class="time-flex">
                                <div class="created_at">建立時間：{{ $item->created_at }}</div>
                                <div class="updated_at">更新時間：{{ $item->updated_at }}</div>
                            </div>
                        </div>
                    @endforeach
                </x-pagination>
            </div>
        </div>
    </main>
@endsection
