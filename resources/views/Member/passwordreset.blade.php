@vite(['resources/scss/app.scss', 'resources/js/index.js'])
@use (App\Lib\I18N\ELanguageText;use App\Lib\I18N\I18N;use App\Lib\Utils\Htmlv2;use App\Lib\Utils\RouteNameField;use Illuminate\Support\Facades\Log)
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
    $dark = $request->cookie('theme');
@endphp
@extends('layouts.default')
@section('title', "重設密碼  | ".Config::get('app.name'))
@section('content')
    <div class="register-frame">
        <form class="register form-ct"
              data-fn="Auth.ResetPassword"
              data-tracks="resetpassword,password_confirmation,password,email,token,token2"
              data-target="#alert"
              data-token="{{ (new \App\Lib\Server\CSRF(RouteNameField::PagePasswordResetPost->value))->get() }}"
              method="POST"
              action="{{ route(RouteNameField::PagePasswordResetPost->value) }}">
            @csrf
            <div class="title">重設密碼</div>
            <div class="row relative !break-keep">
                <label class="col" for="text4">{{$i18N->getLanguage(ELanguageText::validator_field_password)}}</label>
                <div class="col md:w-fit footer:w-full sm:w-full xs:w-full us:w-full">
                    <div class="form-password-group footer:w-full md:w-fit sm:w-full xs:w-full us:w-full">
                        <input id="text4" class="block form-solid front !w-full validate tippyer" data-placement="auto" data-trigger="manual" data-theme="light" data-zindex="19" data-htmlable="true" data-content="<li class='flex flex-nowrap'>⭕必填項目</li><li class='flex flex-nowrap'>🌟獨一無二密碼</li><li class='flex flex-nowrap'>❌最小的長度為8</li>" data-method="required" type="password" minlength="8" maxlength="255" name="password" autocomplete="new-password" required>
                        <div class="btn btn-ripple btn-color7 btn-border-0 back ct" data-fn="password-toggle"
                             data-target="#text4"><i class="fa-regular fa-eye"></i></div>
                    </div>
                </div>
            </div>

            <div class="row relative !break-keep">
                <label class="col" for="text5">{{$i18N->getLanguage(ELanguageText::validator_field_passwordConfirmed)}}</label>
                <div class="col md:w-fit footer:w-full sm:w-full xs:w-full us:w-full">
                    <div class="form-password-group md:w-fit footer:w-full sm:w-full xs:w-full us:w-full">
                        <input id="text5" class="block form-solid front !w-full validate tippyer" data-placement="auto" data-trigger="manual" data-theme="light" data-zindex="19" data-htmlable="true" data-content="<li class='flex flex-nowrap'>⭕必填項目</li><li class='flex flex-nowrap'>🌟確認密碼</li><li class='flex flex-nowrap'>❌最小的長度為8</li>" data-method="required" type="password" minlength="8" name="password_confirmation" autocomplete="new-password"
                               required>
                        <div class="btn btn-ripple btn-color7 btn-border-0 back ct" data-fn="password-toggle"
                             data-target="#text5"><i class="fa-regular fa-eye"></i></div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="email" value="{{ $moreParams['email'] }}">
            <input type="hidden" name="token" value="{{ $moreParams['token'] }}">
            <input type="hidden" name="token2" value="{{ (new \App\Lib\Server\CSRF(RouteNameField::PagePasswordResetPost->value))->get() }}">
            <div class="button">
                <button type="button" name="resetpassword" value="1" class="btn btn-ripple btn-md-strip btn-color7">重設密碼</button>
            </div>
            <div id="alert">
                @if ($errors->any())
                    <x-alert type="danger" :messages="$errors->all()"/>
                @endif
            </div>
        </form>
    </div>
@endsection
