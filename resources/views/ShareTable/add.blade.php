@vite(['resources/css/index.css', 'resources/js/index.js'])
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
    /**
     * @var $newFiles \App\Models\VirtualFile[]
     */
    $newFiles=[];
    if(!empty($moreParams)){
        $newFiles = $moreParams[0]['files'];
    }
@endphp
@extends('layouts.default')
@section('title', "新增分享內容 | ".Config::get('app.name'))
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
    {{--@dump($request)--}}
    <main>
        <div class="container2">
            <form class="form-ct w-full"
                  data-fn="ShareTable.add"
                  data-tracks="files,shareTableName,shareTableType,shareTableDescription,shareTableShortCode,password,password_confirmation,addFile,shareMembers"
                  data-target="#alert"
                  data-token="{{(new CSRF(RouteNameField::APIShareTableItemCreatePost->value))->get()}}"
                  action="{{ route(RouteNameField::APIShareTableItemCreatePost->value) }}"
                  method="post">
                <div class="file-driver">
                @foreach($newFiles as $file)
                    <div class="fd-item">
                        <input type="hidden" name="files[]" value="{{ $file->uuid }}">
                        <div class="fdi-preview">
                        @if(Utilsv2::isSupportImageFile($file->minetypes))
                            <img class="fdi-imginfo" src="{{ $file->getTemporaryUrl() }}" alt="{{ $file->filename }}">
                        @elseif(Utilsv2::isSupportVideoFile($file->minetypes))
                            <video controls src="{{ $file->getTemporaryUrl() }}"></video>
                        @endif
                        </div>
                        <div class="fdi-delete btn btn-circle btn-color7 btn-border-0"><i class="fa-solid fa-xmark"></i></div>
                    </div>
                @endforeach
                </div>
                <div class="share-tables-form">
                    <div class="fdi-content">
                        <input type="submit" name="addFile" value="建立檔案" class="btn btn-ripple btn-color7">
                        <div class="fdic-field">
                            <label for="shareTableName">分享名稱<span class="text-red-500">*</span></label>
                            <input class="form-solid validate tippyer" data-placement="auto" data-trigger="manual" data-theme="light" data-zindex="19" data-htmlable="true" data-content="<li class='flex flex-nowrap'>⭕必填項目</li><li class='flex flex-nowrap'>❌最大的長度為255</li>" data-method="required" type="text" name="shareTableName" maxlength="255" required>
                        </div>
                        <div class="fdic-field">
                            <label for="shareTableDescription">說明</label>
                            <textarea class="form-solid" name="shareTableDescription"></textarea>
                        </div>
                        <div class="fdic-field">
                            <label for="shareTableType">類型</label>
                            <select class="tom-select" data-width="66%" name="shareTableType" required>
                                <option value="public">公開</option>
                                <option value="private">私人</option>
                            </select>
                        </div>
                        <div class="fdic-field">
                            <label for="shareTableShortCode">分享代碼</label>
                            <input class="form-solid validate tippyer"
                                   data-placement="auto" data-trigger="manual" data-theme="light"
                                   data-zindex="19" data-htmlable="true"
                                   data-content="<li class='flex flex-nowrap'>❌最大的長度為255</li>" data-method="default" type="text" name="shareTableShortCode" maxlength="255">
                        </div>
                        <div class="fdic-field">
                            <label for="shareMembers">分享使用者</label>
                            <select class="tom-select" data-src="{{ route(RouteNameField::APIGetUsers->value) }}" data-width="66%" name="shareMembers" multiple required></select>
                        </div>
                        <div class="fdic-field">
                            <label for="password">分享密碼</label>
                            <div class="form-password-group w-2/3">
                                <input id="password" class="block form-solid front !w-full validate tippyer"
                                       data-placement="auto" data-trigger="manual" data-theme="light"
                                       data-zindex="19" data-htmlable="true"
                                       data-content="<li class='flex flex-nowrap'>⭕必填項目</li><li class='flex flex-nowrap'>🌟獨一無二密碼</li><li class='flex flex-nowrap'>❌最小的長度為8</li>"
                                       data-method="default" type="password" maxlength="255"
                                       name="password" autocomplete="new-password">
                                <div class="btn btn-ripple btn-color7 btn-border-0 back ct"
                                     data-fn="password-toggle"
                                     data-target="#password"><i class="fa-regular fa-eye"></i></div>
                            </div>
                        </div>
                        <div class="fdic-field">
                            <label for="password_confirmation">確認密碼</label>
                            <div class="form-password-group w-2/3">
                                <input id="password_confirmation"
                                       class="block form-solid front !w-full validate tippyer"
                                       data-placement="auto" data-trigger="manual" data-theme="light"
                                       data-zindex="19" data-htmlable="true"
                                       data-content="<li class='flex flex-nowrap'>⭕必填項目</li><li class='flex flex-nowrap'>🌟確認密碼</li><li class='flex flex-nowrap'>❌最小的長度為8</li>"
                                       data-method="default" type="password"
                                       name="password_confirmation" autocomplete="new-password">
                                <div class="btn btn-ripple btn-color7 btn-border-0 back ct"
                                     data-fn="password-toggle"
                                     data-target="#password_confirmation"><i class="fa-regular fa-eye"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>
@endsection
