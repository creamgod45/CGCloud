@use (App\Lib\I18N\ELanguageText;use App\Lib\I18N\I18N;use App\Lib\Inventory\EInventoryStatus;use App\Lib\Utils\Htmlv2;use App\Lib\Utils\Utilsv2;use Illuminate\Http\Request;use Illuminate\Support\Facades\Config;use App\Lib\Utils\RouteNameField;use Illuminate\Support\Facades\Storage;use Nette\Utils\Json)
@php
    /***
     * @var string[] $urlParams 當前 URL 參數
     * @var array $moreParams 更多參數
     * @var I18N $i18N I18N 本地化語言系統
     * @var Request $request 請求
     * @var string $fingerprint 客戶端指紋
     * @var string $theme 主題
 * @var \App\Models\ShopConfig[] $styleConfig 系統設定
     * @var \App\Models\Inventory $shopItem
     */
    $menu=false;
    $footer=false;

    $newFiles=[];
    if(!empty($moreParams)){
        $shopItem = $moreParams[0]['shopItem'];
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
@section('title', "編輯商品 ".$shopItem->name." | ".Config::get('app.name'))
@section('head')
    @vite(['resources/css/index.css', 'resources/js/index.js'])
@endsection
@section('content')
    <x-scroll-indicator class="!bottom-[14px]" indicator-target="html"></x-scroll-indicator>
    <main>
        <div class="container3">
            <div class="mt-5 shop-item-add">
                <h1 class="shop-item-add-title noto-serif-tc-bold">
                    編輯商品
                </h1>
                <div class="btn-group-tab shop-item-tab">
                    <div data-tab="#tab1" class="noto-serif-tc-bold tab-btn btn active btn-ripple btn-md-strip">
                        基本設定
                    </div>
                    <div data-tab="#tab3" class="noto-serif-tc-bold tab-btn btn btn-ripple btn-md-strip">進階設定</div>
                    <div data-tab="#tab2" class="noto-serif-tc-bold tab-btn btn btn-ripple btn-md-strip">產品文章</div>
                </div>
                <form class="shop-item-add-form form-common"
                      action="{{ route(RouteNameField::PageAddShopItemPost->value) }}"
                      method="post">
                    <input type="submit" value="更新" class="btn btn-ripple btn-md-strip btn-color7">
                    @csrf
                    <div id="tab1" class="tab-content">
                        <div class="shop-item-add-form-item">
                            <label class="w-1/2 noto-serif-tc-bold" for="ItemName">
                                商品名稱
                                <span class="badges badges-md-strip badges-danger">必填</span>
                            </label>
                            <input class="w-1/2 form-solid" autocomplete="ItemName" value="{{ $shopItem->name }}"
                                   type="text" name="ItemName"
                                   required>
                        </div>
                        <div class="shop-item-add-form-item">
                            <label class="w-1/2 noto-serif-tc-bold" for="ItemStatus">
                                <span class="tippyer" data-content="是否顯示商品">商品啟用</span>
                                <span class="badges badges-md-strip badges-danger">必填</span>
                            </label>
                            <div class="w-1/2">
                                <div id="SC-mode-switcher" data-name="ItemStatus" data-onclick="true"
                                     data-value="{{ $shopItem->enable }}" class="switch">
                                    <div class="switch-border">
                                        <div class="switch-dot"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="shop-item-add-form-item">
                            <div id="ItemPriceTooltip" class="!hidden">
                                <ul>
                                    <li><i class="fa-solid fa-star"></i> 允許使用滾輪控制數值向上增加或向下減少</li>
                                    <li><i class="fa-solid fa-star"></i> 每次滾輪 5.00 單位</li>
                                    <li><i class="fa-solid fa-star"></i> 此欄位不能超過小數點後 2 位</li>
                                    <li><i class="fa-solid fa-star"></i> 此欄位不能有負數</li>
                                    <li><i class="fa-solid fa-star"></i> 當有錯誤格式會在失焦後修正</li>
                                </ul>
                            </div>
                            <label class="w-1/2 noto-serif-tc-bold" for="ItemPrice">
                                <span class="tippyer" data-content="#ItemPriceTooltip"
                                      data-htmlable="true">商品價格</span>
                                <span class="badges badges-md-strip badges-danger">必填</span>
                            </label>
                            <input class="w-1/2 form-solid validate" data-method="number" data-numberofdigits="2"
                                   data-negative="false" data-step="5.00" value="{{ $shopItem->price }}" type="text"
                                   name="ItemPrice"
                                   required>
                        </div>
                        <div class="shop-item-add-form-item">
                            <label class="w-1/2 noto-serif-tc-bold" for="ItemLastPrice">
                                <span class="tippyer" data-content="#ItemPriceTooltip"
                                      data-htmlable="true">商品上個價格</span>
                            </label>
                            <input class="w-1/2 form-solid" data-negative="false" data-step="5.00"
                                   value="{{ $shopItem->last_price }}" readonly type="text" required>
                        </div>
                        <div id="ItemAmountTooltip" class="!hidden">
                            <ul>
                                <li><i class="fa-solid fa-star"></i> 允許使用滾輪控制數值向上增加或向下減少</li>
                                <li><i class="fa-solid fa-star"></i> 每次滾輪 1 單位</li>
                                <li><i class="fa-solid fa-star"></i> 此欄位不能超過小數點後 0 位</li>
                                <li><i class="fa-solid fa-star"></i> 此欄位不能有負數</li>
                                <li><i class="fa-solid fa-star"></i> 當有錯誤格式會在失焦後修正</li>
                            </ul>
                        </div>
                        <div class="shop-item-add-form-item">
                            <label class="w-1/2 noto-serif-tc-bold" for="ItemMaxAmount">
                                <span class="tippyer" data-content="#ItemAmountTooltip"
                                      data-htmlable="true">商品最大數量</span>
                            </label>
                            <input class="w-1/2 form-solid validate" data-method="number" data-numberofdigits="0"
                                   data-negative="false" data-step="1" value="{{ $shopItem->max_amount }}" type="text"
                                   name="ItemMaxAmount"
                                   required>
                        </div>
                        <div class="shop-item-add-form-item">
                            <label class="w-1/2 noto-serif-tc-bold" for="ItemAmount">
                                <span class="tippyer" data-content="#ItemAmountTooltip"
                                      data-htmlable="true">商品現有數量</span>
                                <span class="badges badges-md-strip badges-danger">必填</span>
                            </label>
                            <input class="w-1/2 form-solid validate" data-method="number" data-numberofdigits="0"
                                   data-negative="false" data-step="1" value="{{ $shopItem->amount }}" type="text"
                                   name="ItemAmount" required>
                        </div>
                        <div class="shop-item-add-form-item">
                            <label class="w-1/2 noto-serif-tc-bold" for="ItemPurchasesNum">
                                <span class="tippyer" data-content="#ItemAmountTooltip"
                                      data-htmlable="true">已銷售數量</span>
                            </label>
                            <input class="w-1/2 form-solid validate" data-method="number" data-numberofdigits="0"
                                   data-negative="false" data-step="1" value="{{ $shopItem->purchases_num }}"
                                   type="text" name="ItemPurchasesNum"
                                   required>
                        </div>
                        <div class="shop-item-add-form-item">
                            <label class="w-1/2 noto-serif-tc-bold" for="ItemQuality">
                                存貨品質
                                <span class="badges badges-md-strip badges-danger">必填</span>
                            </label>
                            <select data-width="50%" class="w-1/2 select2" data-value="{{ $shopItem->quality }}"
                                    name="ItemQuality">
                                <option value="new">新品(new)</option>
                                <option value="damaged">受損(damaged)</option>
                                <option value="refurbished">翻新的(refurbished)</option>
                                <option value="used">福利品(used)</option>
                            </select>
                        </div>
                        <div class="shop-item-add-form-item">
                            <label class="w-1/2 noto-serif-tc-bold" for="ItemQuality">
                                存貨狀態
                                <span class="badges badges-md-strip badges-danger">必填</span>
                            </label>
                            <select data-width="50%" class="w-1/2 select2 form-solid"
                                    data-value="{{ $shopItem->status->name }}" name="ItemQuality">
                                @foreach(EInventoryStatus::cases() as $value)
                                    <option value="{{ $value->name }}">{{ $value->value }}({{ $value->name }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="shop-item-add-form-item">
                            <label class="w-1/2 noto-serif-tc-bold" for="ItemTags">標籤</label>
                            <select data-width="50%" data-placeholder="請建立標籤" class="w-1/2 select2-tag"
                                    name="ItemTags" multiple="multiple">
                                @foreach($shopItem->tags as $value)
                                    <option value="{{ $value }}" selected>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div id="tab2" class="!hidden tab-content">
                        <div class="flex flex-wrap">
                            <label class="w-full py-2 noto-serif-tc-bold" for="ItemDescription">
                                商品說明
                                <span class="badges badges-md-strip badges-danger">必填</span>
                            </label>
                            <x-head.tinymce-config name="ItemDescription"
                                                   data-invalidelements="script,head,body,html,meta,link,input,select,textarea,datalist,form,object,embed,applet,style,noscript,plaintext,xmp,listing,comment,title,bgsound,frameset,frame,base,xml"
                                                   data-minheight="700" data-language="zh_TW" data-resize="false"
                                                   data-maxheight="700" novalidate
                                                   parent-class="w-full min-h-[700px]">{{ $shopItem->description }}</x-head.tinymce-config>
                        </div>
                    </div>
                    <div id="tab3" class="!hidden tab-content">
                        <div class="shop-item-add-form-item">
                            <label class="w-1/2 noto-serif-tc-bold" for="ItemBrand">品牌名</label>
                            <input class="w-1/2 form-solid" autocomplete="brand" value="{{ $shopItem->brand }}"
                                   type="text" name="ItemBrand">
                        </div>
                        <div class="shop-item-add-form-item">
                            <label class="w-1/2 noto-serif-tc-bold" for="ItemMPN">製造商零件編號</label>
                            <input class="w-1/2 form-solid" autocomplete="off" value="{{ $shopItem->MPN }}" type="text"
                                   name="ItemMPN">
                        </div>
                        <div class="shop-item-add-form-item">
                            <label class="w-1/2 noto-serif-tc-bold" for="ItemSKU">庫存單位編號</label>
                            <input class="w-1/2 form-solid" autocomplete="off" value="{{ $shopItem->SKU }}" type="text"
                                   name="ItemSKU">
                        </div>
                        <div class="shop-item-add-form-item">
                            <label class="w-1/2 noto-serif-tc-bold" for="ItemGTIN-13">全球商品貿易項目編號</label>
                            <input class="w-1/2 form-solid" autocomplete="off" value="{{ $shopItem->GTIN }}" type="text"
                                   name="ItemGTIN-13">
                        </div>
                        <div class="flex flex-wrap">
                            <div id="ItemImageTooltip" class="!hidden">
                                <ul>
                                    <li><i class="fa-solid fa-star"></i> 必須新增至少一張照片或是影片</li>
                                    <li><i class="fa-solid fa-star"></i> 檔案大小不限制</li>
                                    <li><i class="fa-solid fa-star"></i> 建議檔案大小小於 1 GB(否則會對訪問的使用者有不好的使用體驗)
                                    </li>
                                </ul>
                            </div>
                            <label class="w-full py-2 noto-serif-tc-bold" for="ItemImages">
                                <span class="tippyer" data-htmlable="true"
                                      data-content="#ItemImageTooltip">商品圖片集</span>
                                <span class="badges badges-md-strip badges-danger">必填</span>
                            </label>
                            <div class="uploads_images">
                                @foreach($shopItem->image_url->getImages() as $value)
                                    <div class="img placeholder lazyImg" data-src="{{$value->getUri()}}"></div>
                                    <input type="hidden" name=Images[]" value="{{ $value->getUri() }}">
                                @endforeach
                            </div>
                            <input type="file" class="filepond w-full"
                                   data-allowtypes="image/png::image/jpg::image/jpeg::image/svg+xml::image/gif::image/webp::image/apng::image/bmp::image/avif::video/av1::video/H264::video/H264-SVC::video/H264-RCDO::video/H265::video/JPEG::video/JPEG::video/mpeg::video/mpeg4-generic::video/ogg::video/quicktime::video/JPEG::video/vnd.mpegurl::video/vnd.youtube.yt::video/VP8::video/VP9::video/mp4::video/mp4V-ES::video/MPV::video/vnd.directv.mpeg::video/vnd.dece.mp4::video/vnd.uvvu.mp4::video/H266::video/H263::video/H263-1998::video/H263-2000::video/H261"
                                   @if((!empty($moreParams)))
                                       data-files="{{ Json::encode($newFiles) }}"
                                   @endif
                                   data-upload="{{ route(RouteNameField::APIShareTableItemUploadImage->value) }}"
                                   data-revert="{{ route(RouteNameField::APIShareTableItemUploadImageRevert->value) }}"
                                   data-patch="{{ route(RouteNameField::APIShareTableItemUploadImagePatch->value, ["fileinfo"=>" "]) }}"
                                   data-multiple="true"
                                   name="ItemImages[]"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>
@endsection
