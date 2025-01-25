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
    $content = "";
    $popup = false;
    if(!empty($moreParams)){
        if(isset($moreParams[0]['$content'])){
            $content = $moreParams[0]['$content'];
        }
        if(isset($moreParams[0]['$popup'])){
            $popup = $moreParams[0]['$popup'];
        }
    }
    if($popup){
        $menu=false;
        $footer=false;
    } else {
        $menu=true;
        $footer=true;
    }
@endphp
@extends('layouts.default')
@section('title', "編輯成功  | ".Config::get('app.name'))
@section('content')
    <div class="register-frame">
        <div class="login">
            <div class="title">編輯成功</div>
            <a class="context">{{ $content }}</a>
            <script>
                setTimeout(function(){
                    window.parent.postMessage('close', '*');
                }, 3000);
            </script>
        </div>
    </div>
@endsection
