@use (App\Lib\I18N\ELanguageText;use App\Lib\I18N\I18N;use App\Lib\Utils\Htmlv2;use App\Lib\Utils\Utilsv2;use Illuminate\Http\Request;use Illuminate\Support\Facades\Config;use App\Lib\Utils\RouteNameField;use Illuminate\Support\Facades\Storage;use Nette\Utils\Json)
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
    $menu=false;
    $footer=false;
    $url = "";
    if(!empty($moreParams)){
        if(isset($moreParams[0]['url'])){
            $url = $moreParams[0]['url'];
        }
    }
@endphp
@extends('layouts.default')
@section('title', "影片播放器 | ".Config::get('app.name'))
@section('head')
    @vite(['resources/css/index.css', 'resources/js/index.js'])
@endsection
@section('content')
    <video class="vjs video-js vjs-theme-forest" data-width="100%" data-height="100%" data-type="dash" controls data-src="{{ $url }}"></video>
@endsection
