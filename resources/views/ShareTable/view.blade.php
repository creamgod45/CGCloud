@vite(['resources/scss/app.scss', 'resources/js/index.js'])
@use (App\Lib\I18N\ELanguageText;use App\Lib\I18N\I18N;use App\Lib\Utils\Htmlv2;use App\Lib\Utils\Utilsv2;use App\View\Components\PopoverOptions;use Illuminate\Http\Request;use Illuminate\Pagination\LengthAwarePaginator;use Illuminate\Support\Facades\Config; use Nette\Utils\Json;use App\Lib\Utils\RouteNameField;use App\Lib\Server\CSRF)
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
    $shareTable = null;
    $virtualFiles=[];
    $sharePermissions=[];
    $popup = false;
    $type = "private";
    $passwordProtected = false;
    if(!empty($moreParams)){
        if(isset($moreParams[0]['shareTable'])){
            $shareTable = $moreParams[0]['shareTable'];
        }
        if(isset($moreParams[0]['virtualFiles'])){
            $virtualFiles = $moreParams[0]['virtualFiles'];
        }
        if(isset($moreParams[0]['sharePermissions'])){
            $sharePermissions = $moreParams[0]['sharePermissions'];
        }
        if(isset($moreParams[0]['type'])){
            $type = $moreParams[0]['type'];
        }
        if(isset($moreParams[0]['popup'])){
            $popup = $moreParams[0]['popup'];
            if($popup){
                $menu=false;
                $footer=false;
            }
        }
        if(isset($moreParams[0]['passwordProtected'])){
            $passwordProtected = $moreParams[0]['passwordProtected'];
        }
    }
@endphp
@extends('layouts.default')
@section('title', "檢視 ".$shareTable->name." | ".Config::get('app.name'))
@section('description', $shareTable->description)
@section('head')
    @php
        $image = asset("favicon.png");
        $imageH = 128;
        $imageW = 128;
        if (!$passwordProtected) {
            foreach ($virtualFiles as $item) {
                if(Utilsv2::isSupportImageFile($item->minetypes) && $type === "public"){
                    $image = route(RouteNameField::PagePublicShareTablePreviewItem->value, [ "shortcode" => $shareTable->short_code, "fileId" => $item->uuid ]).".".$item->extension;
                    $image1 = $item->getImage($shareTable->id);
                    if ($image1) {
                        $imageW = $image1->getWidth();
                        $imageH = $image1->getHeight();
                    }
                    break;
                }
            }
        }
    @endphp
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ "檢視 ".$shareTable->name." | ".Config::get('app.name') }}">
    <meta name="twitter:site" content="{{ "@".Config::get("app.url") }}">
    <meta name="twitter:description" content="{{ $shareTable->description }}">
    <meta name="twitter:image" content="{{ $image }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ "檢視 ".$shareTable->name." | ".Config::get('app.name') }}">
    <meta property="og:url" content="{{ Config::get("app.url") }}">
    <meta property="og:image" content="{{ $image }}">
    <meta property="og:image:width" content="{{ $imageW }}">
    <meta property="og:image:height" content="{{ $imageH }}">
    <meta property="og:description" content="{{ $shareTable->description }}">
@endsection
@section('content')
    <x-scroll-indicator indicator-target="body"></x-scroll-indicator>
    @if($passwordProtected)
        {{-- 密碼保護遮罩 --}}
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
            <div class="form-panel-box w-full max-w-md mx-4 bg-white p-2 rounded-lg">
                <div class="flex flex-col items-center gap-4 p-2">
                    <div class="text-4xl text-yellow-400">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <h1 class="form-panel-box-title text-center">此分享資源受密碼保護</h1>
                    <p class="text-sm text-center text-gray-500 dark:text-gray-400">
                        請點擊下方按鈕輸入存取密碼以解鎖 <strong>{{ e($shareTable->name) }}</strong>。
                    </p>
                    <button
                        id="password-unlock-btn"
                        class="password-protected-trigger btn btn-ripple btn-color7 btn-max btn-center"
                        data-shortcode="{{ $shareTable->short_code }}"
                        data-title="{{ e($shareTable->name) }}"
                        data-token="{{ (new CSRF('sharetable.unlock'))->get() }}"
                    >
                        <i class="fa-solid fa-key"></i>&nbsp;輸入密碼解鎖
                    </button>
                </div>
            </div>
        </div>
    @else
        <main>
            <div class="share-table-view">
                <div class="form-panel-box">
                    <h1 class="form-panel-box-title">詳細資訊</h1>
                    <div class="form-content-group">
                        <div class="form-content-header">
                            分享資訊名稱
                        </div>
                        <div class="form-content-content">
                            <input class="form-solid" readonly value="{{ $shareTable->name }}">
                        </div>
                    </div>
                    <div class="form-content-group">
                        <div class="form-content-header">
                            分享資訊說明
                        </div>
                        <div class="form-content-content">
                            <textarea class="form-solid" readonly>{{ $shareTable->description }}</textarea>
                        </div>
                    </div>
                    <div class="form-content-group">
                        <div class="form-content-header">
                            短連接代碼
                        </div>
                        <div class="form-content-content">
                            <input class="form-solid" readonly value="{{ $shareTable->short_code }}">
                        </div>
                    </div>
                    <div class="form-content-group">
                        <div class="form-content-header">
                            分享資訊可見度
                        </div>
                        <div class="form-content-content">
                            <select class="tom-select" data-disabled="true" readonly data-width="50%">
                                <option selected>{{ $shareTable->type }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-content-group">
                        <div class="form-content-header">
                            分享資訊指定授權用戶
                        </div>
                        <div class="form-content-content">
                            <ul>
                                @if($sharePermissions->isEmpty())
                                    <li>{{ $i18N->getLanguage(ELanguageText::ShareTableItemViewNoAuthorizationUser) }}</li>
                                @endif
                                @foreach($sharePermissions as $sharePermission)
                                    @php
                                        $member = $sharePermission->member()->first()@endphp
                                    <li class="link link-hover link-color1 decoration-2">{{ $member->username }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="form-panel-box mb-10">
                    <h1 class="form-panel-box-title">媒體畫廊</h1>
                    <div class="gallery-grid p-4" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
                        @foreach($virtualFiles as $vf)
                            @if(\Illuminate\Support\Str::startsWith($vf->minetypes ?? '', ['image/', 'video/']) && empty($vf['#']))
                                @php
                                    if (isset($vf->size) && is_numeric($vf->size) && $vf->size > 1024 * 1024 * 400) continue;
                                    $tempUrl = $vf->getTemporaryUrl(now()->addMinutes(30), $shareTable->id);
                                @endphp
                                <div class="gallery-item" style="aspect-ratio: 1; overflow: hidden; border-radius: 8px; border: 1px solid #333; background: #000; display: flex; align-items: center; justify-content: center;">
                                    @if(\Illuminate\Support\Str::startsWith($vf->minetypes ?? '', 'video/'))
                                        <video controls preload="metadata" style="width: 100%; height: 100%; object-fit: cover;">
                                            <source src="{{ $tempUrl }}" type="{{ $vf->minetypes }}">
                                        </video>
                                    @else
                                        <img loading="lazy" src="{{ $tempUrl }}" alt="{{ $vf->filename }}" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'"/>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <hr class="bg-slate-300 h-px w-full mx-4">
                    <h1 class="form-panel-box-title">檔案列表</h1>
                    @php
                        $columns = [
                            [ "data" => 'id', "name" => "id", "title" => "ID", "footer" => "ID" ],
                            [ "data" => 'filename', "name" => "filename", "title" => "名稱", "footer" => "名稱" ],
                            [ "data" => 'created_at', "name" => "created_at", "title" => "建立時間" , "footer" => "建立時間" ],
                            [ "data" => 'size', "name" => "size", "title" => "大小", "footer" => "大小" ],
                            [ "data" => 'action', "name" => "action", "title" => "操作", "footer" => "操作" ],
                        ];
                    @endphp
                    <table class="datatable" data-cgdatatype="JSON" data-cgdata="{{ Json::encode($virtualFiles) }}"
                           data-cgcolumns="{{ Json::encode($columns) }}" data-cgfixedtable="true">
                    </table>
                </div>
            </div>
        </main>
    @endif
@endsection
