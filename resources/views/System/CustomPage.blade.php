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
     * @var bool $popup 主題
     * @var \App\Models\CustomizePage $customizePage
     */
    $menu=true;
    $footer=true;
    $popup = false;
    $customizePage = null;
    if(!empty($moreParams)){
        if(isset($moreParams[0]['popup'])){
            $popup = $moreParams[0]['popup'];
            if($popup){
                $menu=false;
                $footer=false;
            }
        }
        if(isset($moreParams[0]['customizePage'])){
            $customizePage = $moreParams[0]['customizePage'];
        }
    }
@endphp
@section('title', "自訂頁面  | ".Config::get('app.name'))
@section('head')
    @vite(['resources/css/index.css', 'resources/js/index.js'])
@endsection
@section('content')
    <x-scroll-indicator class="!bottom-[14px]" indicator-target="html"></x-scroll-indicator>
    <main>
        <div class="container7">
            <form action="" method="post" class="form-common form-ct" data-fn="Custom.Page.Edit" data-tracks="CustomPageName,CustomPageHTML">
                <div class="form-group-flex">
                    <label class="w-1/3 noto-serif-tc-bold" for="CustomPageName">
                        <span>頁面名稱</span>
                        <span class="badges badges-md-strip badges-danger">必填</span>
                    </label>
                    <input class="w-2/3 form-solid" type="text" name="CustomPageName" value="{{ $customizePage->namespace }}" required>
                </div>
                <div class="form-group-flex">
                    <label class="w-1/3 noto-serif-tc-bold" for="CustomPageShowShopList">
                        <span>添加商品列表於此頁面</span>
                        <span class="badges badges-md-strip badges-danger">必填</span>
                    </label>
                    <div class="w-2/3">
                        <div id="SC-mode-switcher" data-name="CustomPageShowShopList" data-onclick="true"
                             class="switch">
                            <div class="switch-border">
                                <div class="switch-dot"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group-flex">
                    <label class="w-1/3 noto-serif-tc-bold" for="CustomPageShowAD">
                        <span>顯示廣告</span>
                        <span class="badges badges-md-strip badges-danger">必填</span>
                    </label>
                    <div class="w-2/3">
                        <div id="SC-mode-switcher" data-name="CustomPageShowAD" data-onclick="true"
                             class="switch">
                            <div class="switch-border">
                                <div class="switch-dot"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="w-full noto-serif-tc-bold" for="CustomPageHTML">
                        <span>頁面設計區塊</span>
                        <span class="badges badges-md-strip badges-danger">必填</span>
                    </label>
                    <x-head.tinymce-config
                        name="CustomPageHTML"
                        data-invalidelements="script,head,body,html,meta,link,input,select,textarea,datalist,form,object,embed,applet,style,noscript,plaintext,xmp,listing,comment,title,bgsound,frameset,frame,base,xml"
                        data-minheight="700" data-language="zh_TW" data-resize="false"
                        data-maxheight="700" novalidate
                        parent-class="w-full mt-5 min-h-[700px]">
                        {{ $customizePage->html }}
                    </x-head.tinymce-config>
                </div>
                <input type="button" class="btn btn-ripple btn-color7 mb-3" value="更新">
            </form>
        </div>
    </main>
@endsection
@extends('layouts.default')
