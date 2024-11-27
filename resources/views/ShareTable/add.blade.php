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
    @dump($request)
    <main>
        <div class="container2">
            <form class="form-ct"
                  data-fn="ShareTable.add"
                  data-tracks="files,shareTableName,shareTableType,shareTableShortCode,password,password_confirmation,addFile"
                  data-target="#alert"
                  data-token="{{(new CSRF(RouteNameField::APIShareTableItemCreatePost->value))->get()}}"
                  action="{{ route(RouteNameField::APIShareTableItemCreatePost->value) }}"
                  method="post">
                <div class="file-driver">
                @foreach($newFiles as $file)
                    <div class="fd-item">
                        <input type="hidden" name="files[{{ $file->id }}]" value="{{ $file->uuid }}">
                        <div class="fdi-preview">
                        @if(Utilsv2::isSupportImageFile($file->minetypes))
                            <img class="fdi-imginfo" src="{{ $file->getTemporaryUrl() }}" alt="{{ $file->filename }}">
                        @elseif(Utilsv2::isSupportVideoFile($file->minetypes))
                            <video controls src="{{ $file->getTemporaryUrl() }}"></video>
                        @endif
                        </div>
                        <div class="fdi-delete btn btn-circle btn-color7 btn-border-0"><i class="fa-solid fa-xmark"></i></div>
                        <div class="fdi-content">
                            <div class="fdic-field">
                                <label for="shareTableName[{{ $file->id }}]">分享名稱</label>
                                <input class="form-solid" type="text" name="shareTableName[{{ $file->id }}]" maxlength="255">
                            </div>
                            <div class="fdic-field">
                                <label for="shareTableDescription[{{ $file->id }}]">說明</label>
                                <textarea class="form-solid" name="shareTableDescription[{{ $file->id }}]"></textarea>
                            </div>
                            <div class="fdic-field">
                                <label for="shareTableType[{{ $file->id }}]">類型</label>
                                <select class="select2" data-width="66%" name="shareTableType[{{ $file->id }}]">
                                    <option value="public">公開</option>
                                    <option value="private">私人</option>
                                </select>
                            </div>
                            <div class="fdic-field">
                                <label for="shareTableShortCode[{{ $file->id }}]">分享代碼</label>
                                <input class="form-solid" type="text" name="shareTableShortCode[{{ $file->id }}]"
                                      maxlength="255">
                            </div>
                            <div class="fdic-field">
                                <label for="password[{{ $file->id }}]">分享密碼</label>
                                <div class="form-password-group w-2/3">
                                    <input id="password[{{ $file->id }}]" class="block form-solid front !w-full validate tippyer"
                                           data-placement="auto" data-trigger="manual" data-theme="light"
                                           data-zindex="19" data-htmlable="true"
                                           data-content="<li class='flex flex-nowrap'>⭕必填項目</li><li class='flex flex-nowrap'>🌟獨一無二密碼</li><li class='flex flex-nowrap'>❌最小的長度為8</li>"
                                           data-method="required" type="password" minlength="8" maxlength="255"
                                           name="password[{{ $file->id }}]" autocomplete="new-password" required>
                                    <div class="btn btn-ripple btn-color7 btn-border-0 back ct"
                                         data-fn="password-toggle"
                                         data-target="#password[{{ $file->id }}]"><i class="fa-regular fa-eye"></i></div>
                                </div>
                            </div>
                            <div class="fdic-field">
                                <label for="password_confirmation[{{ $file->id }}]">確認密碼</label>
                                <div class="form-password-group w-2/3">
                                    <input id="password_confirmation[{{ $file->id }}]" class="block form-solid front !w-full validate tippyer"
                                           data-placement="auto" data-trigger="manual" data-theme="light"
                                           data-zindex="19" data-htmlable="true"
                                           data-content="<li class='flex flex-nowrap'>⭕必填項目</li><li class='flex flex-nowrap'>🌟確認密碼</li><li class='flex flex-nowrap'>❌最小的長度為8</li>"
                                           data-method="required" type="password" minlength="8"
                                           name="password_confirmation[{{ $file->id }}]" autocomplete="new-password"
                                           required>
                                    <div class="btn btn-ripple btn-color7 btn-border-0 back ct"
                                         data-fn="password-toggle"
                                         data-target="#password_confirmation[{{ $file->id }}]"><i class="fa-regular fa-eye"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                </div>
                <input type="submit" name="addFile" value="建立檔案" class="btn btn-primary">
            </form>
        </div>
    </main>
@endsection
