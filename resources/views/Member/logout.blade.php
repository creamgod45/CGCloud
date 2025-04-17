@vite(['resources/css/index.css', 'resources/js/index.js'])
@use (App\Lib\I18N\ELanguageText;use App\Lib\I18N\I18N;use Illuminate\Http\Request;use App\Lib\Utils\RouteNameField)
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

@endphp
@extends('layouts.default')
@section('title', "登出  | ".Config::get('app.name'))
@section('content')
    <div class="register-frame">
        <div class="login">
            <div class="title">{{$i18N->getLanguage(ELanguageText::logout_title)}}</div>
            <a href="{{route(RouteNameField::PageLogin->value)}}"
               class="context">{{$i18N->getLanguage(ELanguageText::logout_context, true)->Replace("%s%", 5)->toString()}}</a>
        </div>
    </div>
    <script>
        setTimeout(() => location.assign("{{route(RouteNameField::PageLogin->value)}}"), 3000);
    </script>
@endsection
