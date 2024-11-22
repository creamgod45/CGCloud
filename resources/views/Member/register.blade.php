@vite(['resources/css/index.css', 'resources/js/index.js'])
@use (App\Lib\I18N\ELanguageText;use App\Lib\I18N\I18N;use App\Lib\Server\CSRF;use Illuminate\Http\Request;use App\Lib\Utils\RouteNameField)
@php
    /***
     * @var string[] $urlParams
     * @var array $moreParams
     * @var I18N $i18N
     * @var Request $request
     * @var string $fingerprint
     */
    $menu=true;
    $footer=true;
@endphp
@extends('layouts.default')
@section('title',  "註冊會員  | ".Config::get('app.name'))
@section('content')
    <div class="register-frame">
        <form class="register form-ct"
              data-fn="Auth.Register"
              data-tracks="username,email,password,password_confirmation,phone,register"
              method="POST"
              data-target="#alert"
              data-token="{{(new CSRF(\App\Lib\Utils\RouteNameField::PageRegisterPost->value))->get()}}"
              action="{{ route(\App\Lib\Utils\RouteNameField::PageRegisterPost->value) }}">
            <input type="hidden" name="_token" id="csrf_token" value="{{csrf_token()}}">
            <div class="title">註冊會員</div>
            <div class="row relative !mt-3 !break-keep">
                <label class="col" for="username">{{$i18N->getLanguage(ELanguageText::validator_field_username)}}</label>
                <input class="col form-solid validate tippyer" data-zindex="19" data-placement="auto" data-trigger="manual" data-theme="light" data-htmlable="true" data-content="<li class='flex flex-nowrap'>⭕必填項目</li><li class='flex flex-nowrap'>🌟獨一無二帳號</li><li class='flex flex-nowrap'>❌最大的長度為255</li>" data-method="required" type="text" name="username" maxlength="255" value="{{old("username")}}" required>
            </div>
            <div class="row relative !break-keep">
                <label class="col" for="email">{{$i18N->getLanguage(ELanguageText::validator_field_email)}}</label>
                <input class="col form-solid validate tippyer" data-zindex="19" data-placement="auto" data-trigger="manual" data-theme="light" data-htmlable="true" data-content="<li class='flex flex-nowrap'>⭕必填項目</li><li class='flex flex-nowrap'>🌟獨一無二電子信箱</li><li class='flex flex-nowrap'>❌最大的長度為320</li>" data-method="email" type="email" name="email" maxlength="320" value="{{old("email")}}"
                       required>
            </div>
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
            <div class="row relative !break-keep form-group">
                <label class="col">{{$i18N->getLanguage(ELanguageText::validator_field_phone)}}</label>
                <div class="col md:max-w-[60%] !block">
                    <input type="tel" class="form-solid ITI !w-full validate tippyer" data-placement="auto" data-trigger="manual" data-theme="light" data-htmlable="true" data-content="<li class='flex flex-nowrap'>⭕必填項目</li><li class='flex flex-nowrap'>🌟獨一無二電話號碼</li><li class='flex flex-nowrap'>⭕收發簡訊的號碼</li><li class='flex flex-nowrap'>❌最小的長度為10</li>" data-method="required" data-zindex="19" minlength="10" name="phone" autocomplete="phone" data-autotrigger="true" data-msg="#phone-Validator-msg" data-true="ok" data-false="failed" required>
                    <span id="phone-Validator-msg" class="hidden"></span>
                </div>
            </div>
            <a class="link" href="{{route(RouteNameField::PageLogin->value)}}">登入會員</a>
            <div class="button">
                <button type="button" name="register" class="btn btn-ripple btn-md-strip btn-warning">註冊</button>
            </div>
            <div id="alert">
                @if ($errors->any())
                    <x-alert type="danger" :messages="$errors->all()"/>
                @endif
            </div>
        </form>
    </div>
@endsection
