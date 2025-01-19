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
    $href = "";
    $content = "";
    if(!empty($moreParams)){
        if(isset($moreParams[0]['$href'])){
            $href = $moreParams[0]['$href'];
        }
        if(isset($moreParams[0]['$content'])){
            $content = $moreParams[0]['$content'];
        }
    }
@endphp
@extends('layouts.default')
@section('title', "刪除成功  | ".Config::get('app.name'))
@section('content')
    <div class="register-frame">
        <div class="login">
            <div class="title">刪除成功</div>
            <a href="{{ $href }}"
               class="context">{{ $content }}</a>
        </div>
    </div>
    <script>
        setTimeout(() => location.assign("{{ $href }}"), 5000);
    </script>
@endsection
