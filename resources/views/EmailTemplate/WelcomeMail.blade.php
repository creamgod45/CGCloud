@vite(['resources/css/WelcomeEmail.css', 'resources/js/WelcomeEmail.js'])
@use (App\Lib\I18N\ELanguageText;use App\Lib\I18N\I18N;use App\Lib\Utils\Htmlv2;use App\Lib\Utils\Utilsv2;use Illuminate\Support\Facades\Log;use App\Lib\Utils\RouteNameField)
@php
    /***
     * @var string[] $urlParams
     * @var array $moreParams
     * @var I18N $i18N
     * @var Request $request
     * @var string $fingerprint
     */
    $menu=false;
    $footer=true;
@endphp

@extends('layouts.default')
@section('title', $i18N->getLanguage(ELanguageText::menu_frontpage))
@section('content')
    <main>
        歡迎 %username%
    </main>
@endsection
