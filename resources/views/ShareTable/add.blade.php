@vite(['resources/css/index.css', 'resources/js/index.js'])
@use (App\Lib\I18N\ELanguageText;use App\Lib\I18N\I18N;use App\Lib\Utils\Htmlv2;use App\Lib\Utils\Utilsv2;use App\View\Components\PopoverOptions;use Illuminate\Http\Request;use Illuminate\Pagination\LengthAwarePaginator;use Illuminate\Support\Facades\Config; use Nette\Utils\Json;use App\Lib\Utils\RouteNameField)
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
    <main>
        <div class="container2">
            @dump($request)
            @dump($newFiles)
            <form>
                <div class="file-driver">
                @foreach($newFiles as $file)
                    <div class="fd-item">
                        @if(Utilsv2::isSupportImageFile($file->minetypes))
                            <img src="{{ $file->getTemporaryUrl() }}" alt="{{ $file->filename }}">
                        @else
                            <a>{{ $file->filename }}</a>
                        @endif
                        <a></a>
                    </div>
                @endforeach
                </div>
                <input type="submit" name="addFile" value="建立檔案" class="btn btn-primary">
            </form>
        </div>
    </main>
@endsection
