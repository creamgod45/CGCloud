@vite(['resources/scss/app.scss', 'resources/js/index.js'])
@use(App\Lib\I18N\ELanguageText;use App\Lib\I18N\I18N;use App\Lib\Utils\RouteNameField;use App\Lib\Server\CSRF)
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
@section('title',  "登入會員  | ".Config::get('app.name'))
@section('content')
    <div class="register-frame">
        <form class="login form-ct"
              data-fn="Auth.login"
              data-tracks="username,password,login,.spinner"
              data-target="#alert"
              data-token="{{(new CSRF(RouteNameField::PageLoginPost->value))->get()}}"
              method="POST"
              action="{{ route(RouteNameField::PageLoginPost->value) }}">
            <input type="hidden" name="_token" id="csrf_token" value="{{csrf_token()}}">
            <div class="title">登入會員</div>
            <div class="row relative !mt-3 !break-keep">
                <label class="col" for="username">{{$i18N->getLanguage(ELanguageText::validator_field_username)}}</label>
                <input class="col form-solid validate tippyer" data-htmlable="true" data-content="<li class='flex flex-nowrap font-bold'>⭕必填項目</li><li class='flex flex-nowrap font-bold'>🌟正確的帳號</li><li class='flex flex-nowrap font-bold'>❌最大的長度為255</li>" data-placement="auto" data-trigger="manual" data-zindex="100" data-theme="light" data-method="required" type="text" name="username" maxlength="255" value="{{old("username")}}" required>
            </div>
            <div class="row relative !mt-3 !break-keep">
                <label class="col" for="password">{{$i18N->getLanguage(ELanguageText::validator_field_password)}}</label>
                <div class="col md:w-fit footer:w-full sm:w-full xs:w-full us:w-full">
                    <div class="form-password-group md:w-fit footer:w-full sm:w-full xs:w-full us:w-full">
                        <input id="text4" class="block form-solid front !w-full validate tippyer" data-htmlable="true" data-content="<li class='flex flex-nowrap font-bold'>⭕必填項目</li><li class='flex flex-nowrap font-bold'>🌟正確的密碼</li><li class='flex flex-nowrap font-bold'>❌最小的長度為8</li>" data-placement="auto" data-theme="light" data-trigger="manual" data-zindex="100" data-method="required" type="password" minlength="8"
                               name="password" autocomplete="password"
                               required>
                        <div class="btn btn-ripple btn-color7 btn-border-0 back ct" data-fn="password-toggle"
                             data-target="#text4"><i class="fa-regular fa-eye"></i></div>
                    </div>
                </div>
            </div>
            <a class="link" href="{{route(RouteNameField::PageForgetPassword->value)}}">忘記密碼</a>
            <a class="link" href="{{route(RouteNameField::PageRegister->value)}}">註冊會員</a>
            <div class="button">
                <button type="submit" name="login" class="btn-ripple btn btn-md-strip btn-success">登入</button>
            </div>
            <div class="spinner hidden">
                <i class="fa-solid fa-spinner animate-spin"></i>&nbsp;處理中
            </div>
            <div id="alert">
                @if ($errors->any())
                    <x-alert type="danger" :messages="$errors->all()"/>
                @endif
            </div>
        </form>
    </div>
@endsection
