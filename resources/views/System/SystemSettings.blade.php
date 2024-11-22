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
     */
    $menu=true;
    $footer=true;

    $newFiles=[];
    if(!empty($moreParams)){
        if(isset($moreParams[0]['ShopConfig'])){
            $shopConfig = $moreParams[0]['ShopConfig'];
        }
        if(isset($moreParams[0]['inventoryTypes'])){
            $inventoryTypes = $moreParams[0]['inventoryTypes'];
        }
        if(isset($moreParams[0]['files'])){
            $files = $moreParams[0]['files'];
            foreach ($files as $item) {
                if(Storage::disk('local')->exists($item)){
                    $newFiles[] = $item;
                }
            }
        }
    }
@endphp
@extends('layouts.default')
@section('title', "系統設定 | ".Config::get('app.name'))
@section('head')
    @vite(['resources/css/index.css', 'resources/js/index.js'])
@endsection
@section('content')
    <x-scroll-indicator class="!bottom-[14px]" indicator-target="html"></x-scroll-indicator>
    <main>
        <div class="container5 rwd" data-fn="system-setting" data-responsivelist=".side-menu,.main-page">
            <div class="side-menu !hidden" data-status="false" data-hideelement=".menu-close-btn">
                <div class="title">
                    <div class="my-3">
                        <div class="menu-close-btn btn btn-ripple btn-error btn-circle"><i
                                class="fa-solid fa-xmark"></i></div>
                        選單
                    </div>
                </div>
                <div class="tab-vertical-group">
                    <div data-tab="#tab1"
                         class="active btn btn-max btn-md-strip btn-ripple tab-btn btn-border-0 btn-pill">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                        <span>常用設定</span>
                    </div>
                    {{-- <div data-tab="#tab2" class="btn btn-max btn-md-strip btn-ripple tab-btn btn-border-0 btn-pill">
                        <i class="fa-solid fa-pen-fancy"></i>
                        <span>自訂首頁設定</span>
                    </div> --}}
                    <div data-tab="#tab3"
                         class="btn btn-max btn-md-strip btn-ripple tab-btn btn-border-0 btn-pill">
                        <i class="fa-solid fa-bars"></i>
                        <span>自訂選單設定</span>
                    </div>
                    <div data-tab="#tab4" class="btn btn-max btn-md-strip btn-ripple tab-btn btn-border-0 btn-pill">
                        <i class="fa-solid fa-warehouse"></i>
                        <span>庫存設定</span>
                    </div>
                    <div data-tab="#tab5" class="btn btn-max btn-md-strip btn-ripple tab-btn btn-border-0 btn-pill">
                        <i class="fa-solid fa-tags"></i>
                        <span>庫存類別設定</span>
                    </div>
                    <div data-tab="#tab6" class="btn btn-max btn-md-strip btn-ripple tab-btn btn-border-0 btn-pill">
                        <i class="fa-brands fa-adversal"></i>
                        <span>廣告設定</span>
                    </div>
                    <div data-tab="#tab7"
                         class="btn btn-max btn-md-strip btn-ripple tab-btn btn-border-0 btn-pill">
                        <i class="fa-solid fa-record-vinyl"></i>
                        <span>系統紀錄</span>
                    </div>
                </div>
            </div>
            <div class="main-page" data-openelement=".menu-open-btn">
                <div class="menu-open-btn btn btn-ripple btn-color7 btn-circle"><i class="fa-solid fa-bars"></i></div>
                <form action="" method="POST" id="tab1" class="tab-panel form-common form-ct"
                      data-fn="Shop.Config.general"
                      data-tracks="ShopName,ShopDescription,ShopMainColor,ShopSecondaryColor,ShopMenuColor,ShopImage,ShopMainTextColor,ShopSecondaryTextColor">
                    @csrf
                    <div class="tab-header">
                        <h3 class="tab-title"><i class="fa-solid fa-wand-magic-sparkles"></i><span>常用設定</span>
                        </h3>
                        <div class="tab-end btn btn-md-strip btn-ok btn-ripple">儲存</div>
                    </div>
                    <div class="tab-body">
                        <h1 class="text-2xl my-2 noto-serif-tc-bold">網站基本設定
                            <hr>
                        </h1>
                        <div class="form-group-flex">
                            <label class="w-1/3 noto-serif-tc-bold" for="ShopName">
                                <span class="tippyer" data-placement="left"
                                      data-content="顯示於多個地方的文字(如網站分享資訊、網頁標題、浮水印圖片、SEO 網站資訊)"
                                      data-htmlable="false">網站名稱</span>
                                <span class="badges badges-md-strip badges-danger">必填</span>
                            </label>
                            <input class="w-2/3 form-solid" type="text" name="ShopName" required>
                        </div>
                        <div class="form-group-flex">
                            <label class="w-1/3 noto-serif-tc-bold" for="ShopDescription">
                                <span class="tippyer" data-placement="left" data-content="顯示於多個地方的文字(如網站分享資訊、SEO 網站資訊)"
                                      data-htmlable="false">網站說明</span>
                                <span class="badges badges-md-strip badges-danger">必填</span>
                            </label>
                            <textarea class="w-2/3 form-solid" rows="10" maxlength="255" name="ShopDescription"
                                      required></textarea>
                        </div>
                        <div class="form-group-flex">
                            <label class="w-1/3 noto-serif-tc-bold" for="ShopMainColor">
                                <span class="tippyer" data-placement="left" data-content="顯示於網站主要顏色"
                                      data-htmlable="false">網站主要顏色</span>
                                <span class="badges badges-md-strip badges-danger">必填</span>
                            </label>
                            <input class="w-2/3 form-color btn btn-ripple btn-color7 btn-border-0" type="color"
                                   name="ShopMainColor" required>
                        </div>
                        <div class="form-group-flex">
                            <label class="w-1/3 noto-serif-tc-bold" for="ShopMainTextColor">
                                <span class="tippyer" data-placement="left" data-content="顯示於網站主要文字顏色"
                                      data-htmlable="false">網站主要文字顏色</span>
                                <span class="badges badges-md-strip badges-danger">必填</span>
                            </label>
                            <input class="w-2/3 form-color btn btn-ripple btn-color7 btn-border-0" type="color"
                                   name="ShopMainTextColor" required>
                        </div>
                        <div class="form-group-flex">
                            <label class="w-1/3 noto-serif-tc-bold" for="ShopSecondaryColor">
                                <span class="tippyer" data-placement="left" data-content="顯示於網站次要顏色"
                                      data-htmlable="false">網站次要顏色</span>
                                <span class="badges badges-md-strip badges-danger">必填</span>
                            </label>
                            <input class="w-2/3 form-color btn btn-ripple btn-color7 btn-border-0" type="color"
                                   name="ShopSecondaryColor" required>
                        </div>
                        <div class="form-group-flex">
                            <label class="w-1/3 noto-serif-tc-bold" for="ShopSecondaryTextColor">
                                <span class="tippyer" data-placement="left" data-content="顯示於網站次要文字顏色"
                                      data-htmlable="false">網站次要文字顏色</span>
                                <span class="badges badges-md-strip badges-danger">必填</span>
                            </label>
                            <input class="w-2/3 form-color btn btn-ripple btn-color7 btn-border-0" type="color"
                                   name="ShopSecondaryTextColor" required>
                        </div>
                        <div class="form-group-flex">
                            <label class="w-1/3 noto-serif-tc-bold" for="ShopMenuColor">
                                <span class="tippyer" data-placement="left" data-content="顯示於網站標題列顏色"
                                      data-htmlable="false">網站標題列顏色</span>
                                <span class="badges badges-md-strip badges-danger">必填</span>
                            </label>
                            <input class="w-2/3 form-color btn btn-ripple btn-color7 btn-border-0" type="color"
                                   name="ShopMenuColor" required>
                        </div>
                        <div class="form-group-flex">
                            <label class="w-1/3 noto-serif-tc-bold" for="ShopImage">
                                <span class="tippyer" data-placement="left"
                                      data-content="顯示於多個地方的圖片(如網站分享資訊、SEO 網站資訊、Logo)"
                                      data-htmlable="false">網站圖片</span>
                                <span class="badges badges-md-strip badges-danger">必填</span>
                            </label>
                            <input type="hidden" name="ShopImageAttachment" value="0">
                            <input type="file"
                                   class="filepond w-2/3"
                                   data-allowtypes="image/png::image/jpg::image/jpeg::image/svg+xml::image/gif::image/webp::image/apng::image/bmp::image/avif"
                                   @if((!empty($moreParams)))
                                       data-files="{{ Json::encode($newFiles) }}"
                                   @endif
                                   data-upload="{{ route(RouteNameField::APISystemSettingUpload->value) }}"
                                   data-revert="{{ route(RouteNameField::APIShareTableItemUploadImageRevert->value) }}"
                                   data-patch="{{ route(RouteNameField::APIShareTableItemUploadImagePatch->value, ["fileinfo"=>" "]) }}"
                                   name="ShopImage[]"/>
                        </div>
                        <h1 class="text-2xl my-2 noto-serif-tc-bold">超連接設定
                            <hr>
                        </h1>
                        <div class="form-group-flex">
                            <label class="w-1/3 noto-serif-tc-bold" for="ShopInfoFacebook">
                                <span class="tippyer" data-placement="left"
                                      data-content="設定網站的 Facebook 超連接，如果沒有填入資料時會自動隱藏"
                                      data-htmlable="false">Facebook 超連接</span>
                                <span class="badges badges-md-strip badges-danger">必填</span>
                            </label>
                            <input class="w-2/3 form-solid" type="url" name="ShopInfoYoutube" required>
                        </div>
                        <div class="form-group-flex">
                            <label class="w-1/3 noto-serif-tc-bold" for="ShopInfoFacebook">
                                <span class="tippyer" data-placement="left"
                                      data-content="設定網站的 Youtube 超連接，如果沒有填入資料時會自動隱藏"
                                      data-htmlable="false">Youtube 超連接</span>
                                <span class="badges badges-md-strip badges-danger">必填</span>
                            </label>
                            <input class="w-2/3 form-solid" type="url" name="ShopInfoYoutube" required>
                        </div>
                        <div class="form-group-flex">
                            <label class="w-1/3 noto-serif-tc-bold" for="ShopInfoX">
                                <span class="tippyer" data-placement="left"
                                      data-content="設定網站的 X 超連接，如果沒有填入資料時會自動隱藏"
                                      data-htmlable="false">X 超連接</span>
                                <span class="badges badges-md-strip badges-danger">必填</span>
                            </label>
                            <input class="w-2/3 form-solid" type="url" name="ShopInfoX" required>
                        </div>
                        <div class="form-group-flex">
                            <label class="w-1/3 noto-serif-tc-bold" for="ShopInfoInstagram">
                                <span class="tippyer" data-placement="left"
                                      data-content="設定網站的 Instagram 超連接，如果沒有填入資料時會自動隱藏"
                                      data-htmlable="false">Instagram 超連接</span>
                                <span class="badges badges-md-strip badges-danger">必填</span>
                            </label>
                            <input class="w-2/3 form-solid" type="url" name="ShopInfoInstagram" required>
                        </div>
                        <div class="form-group-flex">
                            <label class="w-1/3 noto-serif-tc-bold" for="ShopInfoThread">
                                <span class="tippyer" data-placement="left"
                                      data-content="設定網站的 Thread 超連接，如果沒有填入資料時會自動隱藏"
                                      data-htmlable="false">Thread 超連接</span>
                                <span class="badges badges-md-strip badges-danger">必填</span>
                            </label>
                            <input class="w-2/3 form-solid" type="url" name="ShopInfoThread" required>
                        </div>
                        <div class="form-group-flex">
                            <label class="w-1/3 noto-serif-tc-bold" for="ShopInfoMap">
                                <span class="tippyer" data-placement="left"
                                      data-content="設定網站的 地圖 超連接，如果沒有填入資料時會自動隱藏"
                                      data-htmlable="false">Google 地圖 超連接</span>
                                <span class="badges badges-md-strip badges-danger">必填</span>
                            </label>
                            <input class="w-2/3 form-solid" type="url" name="ShopInfoMap" required>
                        </div>
                        <div class="form-group-flex">
                            <label class="w-1/3 noto-serif-tc-bold" for="ShopInfoMap">
                                <span class="tippyer" data-placement="left"
                                      data-content="設定網站的 地圖 超連接，如果沒有填入資料時會自動隱藏"
                                      data-htmlable="false">Apple 地圖 超連接</span>
                                <span class="badges badges-md-strip badges-danger">必填</span>
                            </label>
                            <input class="w-2/3 form-solid" type="url" name="ShopInfoMap" required>
                        </div>
                    </div>
                </form>
                {{-- <form action="" method="POST" id="tab2" class="!hidden tab-panel form-common form-ct" data-fn="Shop.Config.general"
                      data-tracks="HomeShowShopList,HomeDesignPanel,ContractDesignPanel">
                    <div class="tab-header">
                        <h3 class="tab-title"><i
                                class="fa-solid fa-pen-fancy"></i><span>自訂首頁設定</span></h3>
                        <div class="tab-end btn btn-md-strip btn-ok btn-ripple">儲存</div>
                    </div>
                    <div class="tab-body">
                        <div class="form-group-flex">
                            <label class="w-1/3 noto-serif-tc-bold relative" for="HomeShowShopList">
                                <span class="tippyer" data-placement="left"
                                      data-content="訪問網站後的根網址首頁直接顯示商品列表，如({{ Config::get('app.url') }})"
                                      data-htmlable="true">首頁顯示商品列表</span>
                                <span class="badges badges-md-strip badges-danger">必填</span>
                            </label>
                            <div class="w-2/3">
                                <div id="SC-mode-switcher" data-name="HomeShowShopList" data-onclick="true"
                                     class="switch">
                                    <div class="switch-border">
                                        <div class="switch-dot"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-flex">
                            <label class="w-1/3 noto-serif-tc-bold" for="CustomPageNameList">
                                <span class="tippyer" data-placement="left" data-content="請幫需要自訂頁面命名"
                                      data-htmlable="false">自訂頁面設定</span>
                                <span class="badges badges-md-strip badges-danger">必填</span>
                            </label>
                            <select data-width="66.666667%" data-placeholder="請建立頁面名稱" class="w-1/2 select2-tag"
                                    name="CustomPageNameList" multiple="multiple">
                                <option value="到府服務" selected>到府服務</option>
                            </select>
                        </div>
                        <div class="form-group-flex">
                            <label class="w-1/3 pl-5 noto-serif-tc-bold" for="CustomPageNameList">
                                <i class="fa-brands fa-hashnode"></i>
                                <span class="tippyer" data-placement="left" data-content="點擊編輯按鈕自訂頁面的內容"
                                      data-htmlable="false">到府服務</span>
                                <span class="badges badges-md-strip badges-danger">必填</span>
                            </label>
                            <div class="btn btn-ripple btn-color7">編輯</div>
                        </div>
                        <div class="form-group">
                            <label class="w-full mb-5 noto-serif-tc-bold relative" for="HomeDesignPanel">
                                <span class="tippyer" data-placement="left" data-content="這裡可以設定網站首頁的內容，可以自行創作文章與版面設計"
                                      data-htmlable="true">首頁設計區塊</span>
                                <span class="badges badges-md-strip badges-danger">必填</span>
                            </label>
                            <x-head.tinymce-config
                                name="HomeDesignPanel"
                                data-invalidelements="script,head,body,html,meta,link,input,select,textarea,datalist,form,object,embed,applet,style,noscript,plaintext,xmp,listing,comment,title,bgsound,frameset,frame,base,xml"
                                data-minheight="700" data-language="zh_TW" data-resize="false"
                                data-maxheight="700" novalidate
                                parent-class="w-full mt-5 min-h-[700px]">
                            </x-head.tinymce-config>
                        </div>
                        <div class="form-group">
                            <label class="w-full mb-5 noto-serif-tc-bold relative" for="ContractDesignPanel">
                                <span class="tippyer" data-placement="left"
                                      data-content="這裡可以設定網站聯絡我們的內容，可以自行創作文章與版面設計"
                                      data-htmlable="true">聯絡我們</span>
                                <span class="badges badges-md-strip badges-danger">必填</span>
                            </label>
                            <x-head.tinymce-config
                                name="ContractDesignPanel"
                                data-invalidelements="script,head,body,html,meta,link,input,select,textarea,datalist,form,object,embed,applet,style,noscript,plaintext,xmp,listing,comment,title,bgsound,frameset,frame,base,xml"
                                data-minheight="700" data-language="zh_TW" data-resize="false"
                                data-maxheight="700" novalidate
                                parent-class="w-full mt-5 min-h-[700px]">
                            </x-head.tinymce-config>
                        </div>
                    </div>
                </form> --}}
                <form action="" method="POST" id="tab3" class="!hidden tab-panel form-common form-ct"
                      data-fn="Shop.Config.general"
                      data-tracks="ShopMenuList">
                    <div class="tab-header">
                        <h3 class="tab-title"><i
                                class="fa-solid fa-bars"></i><span>自訂選單設定</span></h3>
                        <div class="tab-end btn btn-md-strip btn-ok btn-ripple">儲存</div>
                    </div>
                    <div class="tab-body">
                        <div class="organizable-panel organizable-vertical-menu">
                            <div class="organizable-flex organizable-color7 organizable">
                                <div class="title">首頁</div>
                                <div class="organizable-delete btn btn-error btn-ripple btn-circle">
                                    <i class="fa-solid fa-xmark"></i>
                                </div>
                                <div class="organizable-point btn btn-color7-50 btn-ripple btn-circle">
                                    <i class="fa-solid fa-grip-vertical"></i>
                                </div>
                            </div>
                            <div class="organizable-flex organizable-color7 organizable">
                                <div class="title">聯絡我們</div>
                                <div class="organizable-delete btn btn-error btn-ripple btn-circle">
                                    <i class="fa-solid fa-xmark"></i>
                                </div>
                                <div class="organizable-point btn btn-color7-50 btn-ripple btn-circle">
                                    <i class="fa-solid fa-grip-vertical"></i>
                                </div>
                            </div>
                            <div class="organizable-flex organizable-color7 organizable">
                                <div class="title">線上估價</div>
                                <div class="organizable-delete btn btn-error btn-ripple btn-circle">
                                    <i class="fa-solid fa-xmark"></i>
                                </div>
                                <div class="organizable-point btn btn-color7-50 btn-ripple btn-circle">
                                    <i class="fa-solid fa-grip-vertical"></i>
                                </div>
                            </div>
                            <div class="organizable-vertical-menu organizable-panel organizable">
                                <div class="organizable-flex organizable-color7 organizable">
                                    <div class="title">套裝電腦</div>
                                    <div class="organizable-delete btn btn-error btn-ripple btn-circle">
                                        <i class="fa-solid fa-xmark"></i>
                                    </div>
                                    <div class="organizable-point btn btn-color7-50 btn-ripple btn-circle">
                                        <i class="fa-solid fa-grip-vertical"></i>
                                    </div>
                                </div>
                                <div class="organizable-flex organizable-color7 organizable">
                                    <div class="title">套裝電腦</div>
                                    <div class="organizable-delete btn btn-error btn-ripple btn-circle">
                                        <i class="fa-solid fa-xmark"></i>
                                    </div>
                                    <div class="organizable-point btn btn-color7-50 btn-ripple btn-circle">
                                        <i class="fa-solid fa-grip-vertical"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="organizable-flex organizable-color7 organizable">
                                <div class="title">到府服務</div>
                                <div class="organizable-delete btn btn-error btn-ripple btn-circle">
                                    <i class="fa-solid fa-xmark"></i>
                                </div>
                                <div class="organizable-point btn btn-color7-50 btn-ripple btn-circle">
                                    <i class="fa-solid fa-grip-vertical"></i>
                                </div>
                            </div>
                            <div class="organizable-flex organizable-color7 organizable">
                                <div class="title">全台據點</div>
                                <div class="organizable-delete btn btn-error btn-ripple btn-circle">
                                    <i class="fa-solid fa-xmark"></i>
                                </div>
                                <div class="organizable-point btn btn-color7-50 btn-ripple btn-circle">
                                    <i class="fa-solid fa-grip-vertical"></i>
                                </div>
                            </div>
                            <div class="organizable-flex organizable-color7 organizable">
                                <div class="title">購買流程</div>
                                <div class="organizable-delete btn btn-error btn-ripple btn-circle">
                                    <i class="fa-solid fa-xmark"></i>
                                </div>
                                <div class="organizable-point btn btn-color7-50 btn-ripple btn-circle">
                                    <i class="fa-solid fa-grip-vertical"></i>
                                </div>
                            </div>
                            <div class="organizable-flex organizable-color7 organizable">
                                <div class="title">新增產品</div>
                                <div class="organizable-delete btn btn-error btn-ripple btn-circle">
                                    <i class="fa-solid fa-xmark"></i>
                                </div>
                                <div class="organizable-point btn btn-color7-50 btn-ripple btn-circle">
                                    <i class="fa-solid fa-grip-vertical"></i>
                                </div>
                            </div>
                            <div class="organizable-flex organizable-color7 organizable">
                                <div class="title">庫存管理</div>
                                <div class="organizable-delete btn btn-error btn-ripple btn-circle">
                                    <i class="fa-solid fa-xmark"></i>
                                </div>
                                <div class="organizable-point btn btn-color7-50 btn-ripple btn-circle">
                                    <i class="fa-solid fa-grip-vertical"></i>
                                </div>
                            </div>
                            <div class="organizable-flex organizable-color7 organizable">
                                <div class="title">系統管理</div>
                                <div class="organizable-delete btn btn-error btn-ripple btn-circle">
                                    <i class="fa-solid fa-xmark"></i>
                                </div>
                                <div class="organizable-point btn btn-color7-50 btn-ripple btn-circle">
                                    <i class="fa-solid fa-grip-vertical"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <form action="" method="POST" id="tab4" class="!hidden tab-panel form-common form-ct"
                      data-fn="Shop.Config.general"
                      data-tracks="ShopItemAmountWhenZeroIsCustomer,ShopItemAmountWhenZeroIsSystem">
                    <div class="tab-header">
                        <h3 class="tab-title"><i
                                class="fa-solid fa-warehouse"></i><span>庫存設定</span></h3>
                        <div class="tab-end btn btn-md-strip btn-ok btn-ripple">儲存</div>
                    </div>
                    <div class="tab-body">
                        <div class="form-group-flex">
                            <label class="w-1/3 noto-serif-tc-bold" for="ShopItemAmountWhenZeroIsCustomer">
                                <span class="tippyer" data-placement="left"
                                      data-content="當商品庫存沒有時系統該如何處理?如果商品本身有設定此功能會忽略此設定值"
                                      data-htmlable="false">客人操作商品沒有庫存時?</span>
                                <span class="badges badges-md-strip badges-danger">必填</span>
                            </label>
                            <select data-width="66.666667%" class="w-2/3 select2"
                                    name="ShopItemAmountWhenZeroIsCustomer">
                                <option value="off">關閉顯示商品</option>
                                <option value="visible">可以看到但無法選擇</option>
                                <option value="on">依然可以選擇</option>
                            </select>
                        </div>
                        <div class="form-group-flex">
                            <label class="w-1/3 noto-serif-tc-bold" for="ShopItemAmountWhenZeroIsSystem">
                                <span class="tippyer" data-placement="left"
                                      data-content="當商品庫存沒有時想要系統什麼處理方式，如果商品本身有設定此功能會忽略此設定值"
                                      data-htmlable="false">系統自動操作商品沒有庫存時?</span>
                                <span class="badges badges-md-strip badges-danger">必填</span>
                            </label>
                            <select data-width="66.666667%" class="w-2/3 select2" name="ShopItemAmountWhenZeroIsSystem">
                                <option value="Out_of_stock">自動更改商品為缺貨</option>
                                <option value="none">不處理</option>
                            </select>
                        </div>
                    </div>
                </form>
                <form action="" method="POST" id="tab5" class="!hidden tab-panel form-common form-ct"
                      data-fn="Shop.Config.general"
                      data-tracks="CustomInventoryTags,CustomInventoryTagNamespace,CustomInventoryTagName,CustomInventoryTagDescription">
                    <div class="tab-header">
                        <h3 class="tab-title"><i
                                class="fa-solid fa-tags"></i><span>庫存類別設定</span></h3>
                        <div class="tab-end btn btn-md-strip btn-ok btn-ripple">儲存</div>
                    </div>
                    <div class="tab-body">
                        <div class="form-group-flex">
                            <label class="w-1/3 noto-serif-tc-bold" for="CustomInventoryTags">
                                <span class="tippyer" data-placement="left" data-content="請選擇現有或是自訂的類型，進行編輯"
                                      data-htmlable="false">選擇已有類型或是自訂類型</span>
                                <span class="badges badges-md-strip badges-danger">必填</span>
                            </label>
                            <select data-width="66.666667%" data-placeholder="請選擇現有或是自訂的類型，進行編輯"
                                    class="w-1/2 select2-tag"
                                    name="CustomInventoryTags">
                                @foreach($inventoryTypes as $item)
                                    <option value="{{ $item->namespace }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <hr>
                        <div class="form-group-flex">
                            <label class="w-1/3 noto-serif-tc-bold" for="CustomInventoryTagNamespace">
                                <span class="tippyer" data-placement="left" data-content="儲存於資料庫中的值"
                                      data-htmlable="false">類型值</span>
                                <span class="badges badges-md-strip badges-danger">必填</span>
                            </label>
                            <input class="w-2/3 form-solid" type="text" name="CustomInventoryTagName" required>
                        </div>
                        <div class="form-group-flex">
                            <label class="w-1/3 noto-serif-tc-bold" for="CustomInventoryTagName">
                                <span class="tippyer" data-placement="left" data-content="用於顯示的文字"
                                      data-htmlable="false">顯示類型名稱</span>
                                <span class="badges badges-md-strip badges-danger">必填</span>
                            </label>
                            <input class="w-2/3 form-solid" type="text" name="CustomInventoryTagName" required>
                        </div>
                        <div class="form-group-flex">
                            <label class="w-1/3 noto-serif-tc-bold" for="CustomInventoryTagDescription">
                                <span class="tippyer" data-placement="left" data-content="用於描述此分類"
                                      data-htmlable="false">類型說明</span>
                                <span class="badges badges-md-strip badges-danger">必填</span>
                            </label>
                            <textarea class="w-2/3 form-solid" rows="10" name="CustomInventoryTagDescription"
                                      required></textarea>
                        </div>
                    </div>
                </form>
                <form action="" method="POST" id="tab6" class="!hidden tab-panel form-common form-ct"
                      data-fn="Shop.Config.general"
                      data-tracks="ShopAdPopupEnable,ShopAdPopup,ShopAdItemEnable,ShopAdItem">
                    <div class="tab-header">
                        <h3 class="tab-title"><i
                                class="fa-brands fa-adversal"></i><span>廣告設定</span></h3>
                        <div class="tab-end btn btn-md-strip btn-ok btn-ripple">儲存</div>
                    </div>
                    <div class="tab-body">
                        <div class="form-group-flex">
                            <label class="w-1/3 noto-serif-tc-bold relative" for="ShopAdPopupEnable">
                                <span class="tippyer" data-placement="left" data-content="是否啟用顯示廣告橫幅 1(彈出式廣告圖片)"
                                      data-htmlable="true">顯示廣告橫幅 1(彈出式廣告圖片)</span>
                                <span class="badges badges-md-strip badges-danger">必填</span>
                            </label>
                            <div class="w-2/3">
                                <div id="SC-mode-switcher" data-name="ShopAdPopupEnable" data-onclick="true"
                                     class="switch">
                                    <div class="switch-border">
                                        <div class="switch-dot"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-flex">
                            <label class="w-1/3 noto-serif-tc-bold" for="ShopAdPopup">
                                <span class="tippyer" data-placement="left" data-content="顯示於網站固定的欄位，網站載入時彈出的畫面"
                                      data-htmlable="false">廣告橫幅 1(彈出式廣告圖片)</span>
                                <span class="badges badges-md-strip badges-danger">必填</span>
                            </label>
                            <input type="hidden" name="ShopAdPopupAttachment" value="0">
                            <input type="file"
                                   class="filepond w-2/3"
                                   data-allowtypes="image/png::image/jpg::image/jpeg::image/svg+xml::image/gif::image/webp::image/apng::image/bmp::image/avif"
                                   @if((!empty($moreParams)))
                                       data-files="{{ Json::encode($newFiles) }}"
                                   @endif
                                   data-upload="{{ route(RouteNameField::APISystemSettingUpload->value) }}"
                                   data-revert="{{ route(RouteNameField::APIShareTableItemUploadImageRevert->value) }}"
                                   data-patch="{{ route(RouteNameField::APIShareTableItemUploadImagePatch->value, ["fileinfo"=>" "]) }}"
                                   name="ShopAdPopup[]"/>
                        </div>
                        <div class="form-group-flex">
                            <label class="w-1/3 noto-serif-tc-bold relative" for="ShopAdItemEnable">
                                <span class="tippyer" data-placement="left" data-content="是否啟用顯示廣告橫幅 2(單一商品廣告)"
                                      data-htmlable="true">顯示廣告橫幅 2(單一商品廣告)</span>
                                <span class="badges badges-md-strip badges-danger">必填</span>
                            </label>
                            <div class="w-2/3">
                                <div id="SC-mode-switcher" data-name="ShopAdItemEnable" data-onclick="true"
                                     class="switch">
                                    <div class="switch-border">
                                        <div class="switch-dot"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-flex">
                            <label class="w-1/3 noto-serif-tc-bold" for="ShopAdItem">
                                <span class="tippyer" data-placement="left"
                                      data-content="顯示於網站固定的欄位，網站載入時會在商品內容乓邊出現單一橫條廣告的畫面"
                                      data-htmlable="false">廣告橫幅 2(單一商品廣告)</span>
                                <span class="badges badges-md-strip badges-danger">必填</span>
                            </label>
                            <input type="hidden" name="ShopAdItemAttachment" value="0">
                            <input type="file"
                                   class="filepond w-2/3"
                                   data-allowtypes="image/png::image/jpg::image/jpeg::image/svg+xml::image/gif::image/webp::image/apng::image/bmp::image/avif"
                                   @if((!empty($moreParams)))
                                       data-files="{{ Json::encode($newFiles) }}"
                                   @endif
                                   data-upload="{{ route(RouteNameField::APISystemSettingUpload->value) }}"
                                   data-revert="{{ route(RouteNameField::APIShareTableItemUploadImageRevert->value) }}"
                                   data-patch="{{ route(RouteNameField::APIShareTableItemUploadImagePatch->value, ["fileinfo"=>" "]) }}"
                                   name="ShopAdItem[]"/>
                        </div>
                    </div>
                </form>
                <div id="tab7" class="!hidden tab-panel">
                    <div class="tab-header">
                        <h3 class="tab-title"><i class="fa-solid fa-record-vinyl"></i><span>系統紀錄</span></h3>
                    </div>
                    <div class="tab-body">
                        @php
                            $popover = "PW_".\Illuminate\Support\Str::random();
                            $tableOption = new \App\View\Components\TableOption(
                                '',
                                [
                                    [ "data" => 'id', "name" => "id", "title" => "ID", "footer" => "ID" ],
                                    [ "data" => 'type', "name" => "type", "title" => "類型", "footer" => "類型" ],
                                    [ "data" => 'title', "name" => "title", "title" => "主題" , "footer" => "主題" ],
                                    [ "data" => 'description', "name" => "description", "title" => "說明", "footer" => "說明" ],
                                    [ "data" => 'UUID', "name" => "UUID", "title" => "用戶身分", "footer" => "用戶身分" ],
                                    [ "data" => 'FingerPrint', "name" => "FingerPrint", "title" => "指紋碼", "footer" => "指紋碼" ],
                                    [ "data" => 'created_at', "name" => "created_at", "title" => "建立時間", "footer" => "建立時間" ],
                                ],
                                'ServerSide',
                                [
                                    "url" => route(RouteNameField::APISystemLogs->value),
                                    "type" => 'POST',
                                    "timeout" => 100000,
                                    "headers" => [
                                        "X-CSRF-TOKEN" => csrf_token()
                                    ]
                                ],
                                responsive: true,
                                scrollY: 260,
                                fixedHeader: true,
                                fixedFooter: true,
                                paging: false,
                            );
                        @endphp
                        <x-data-table :table-option="$tableOption"></x-data-table>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
