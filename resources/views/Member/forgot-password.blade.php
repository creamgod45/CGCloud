@use(App\Lib\I18N\ELanguageText;use App\Lib\I18N\I18N;use App\Lib\Utils\RouteNameField)
@vite(['resources/scss/app.scss', 'resources/js/index.js'])
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
@section('title', "忘記密碼 | ".Config::get('app.name'))
@section('content')
    <div class="register-frame">
        <form class="register form-ct"
              data-fn="Auth.ForgetPassword"
              data-tracks="email,forget"
              data-target="#alert"
              data-token="{{ (new \App\Lib\Server\CSRF(RouteNameField::PageForgetPasswordPost->value))->get() }}"
              method="POST"
              action="{{ route(RouteNameField::PageForgetPasswordPost->value) }}">
            @csrf
            <div class="title">忘記密碼</div>
            <div class="row relative">
                <label class="col">{{$i18N->getLanguage(ELanguageText::validator_field_email)}}</label>
                <input type="email" name="email" class="col form-solid validate validate tippyer" data-zindex="19" data-placement="auto" data-trigger="manual" data-theme="light" data-htmlable="true" data-content="<li class='flex flex-nowrap'>⭕必填項目</li><li class='flex flex-nowrap'>🌟獨一無二電子信箱</li><li class='flex flex-nowrap'>❌最大的長度為320</li>" data-method="email" value="{{old("email")}}" required>
            </div>
            <div class="button">
                <button type="button" name="forget" value="1" class="btn btn-color7 btn-ripple btn-md">發送重設密碼連接</button>
            </div>
            @if(session("status"))
                {{session("status")}}
            @endif
            <div id="alert">
            @if ($errors->any())
                <x-alert type="danger" :messages="$errors->all()"/>
            @endif
            </div>
        </form>
    </div>
@endsection
