@vite(['resources/css/profile.css', 'resources/js/profile.js'])
@use (App\Lib\I18N\ELanguageText;use App\Lib\I18N\I18N;use Illuminate\Contracts\Pagination\LengthAwarePaginator)
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
    $members = $moreParams['members'];

@endphp
@extends('layouts.default')
@section('title', "會員資料  | ".Config::get('app.name'))
@section('content')
    <main class="container1">
        <div class="home">
            <x-pagination :i18N="$i18N" :elements="$members" :nopaginationframe="0" :header-page-action="true">
                <table class="table table-row-hover table-striped">
                    <thead>
                    <tr>
                        <th>ID<span><i class="fa-solid fa-sort-down"></i></span></th>
                        <th>{{$i18N->getLanguage(ELanguageText::validator_field_username)}} <span><i
                                    class="fa-solid fa-sort-down"></i></span></th>
                        <th>{{$i18N->getLanguage(ELanguageText::validator_field_email)}} <span>
                        <i class="fa-solid fa-sort-down"></i></span></th>
                        <th>{{$i18N->getLanguage(ELanguageText::validator_field_phone)}} <span>
                        <i class="fa-solid fa-sort-down"></i></span></th>
                        <th>{{$i18N->getLanguage(ELanguageText::validator_field_enable)}} <span>
                        <i class="fa-solid fa-sort-down"></i></span></th>
                        <th>{{$i18N->getLanguage(ELanguageText::validator_field_administrator)}} <span><i
                                    class="fa-solid fa-sort-down"></i></span></th>
                        <th>{{$i18N->getLanguage(ELanguageText::validator_field_email_verified_at)}} <span><i
                                    class="fa-solid fa-sort-down"></i></span></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($members as $member)
                        <tr>
                            <th>{{ $member->id }}</th>
                            <th>{{ $member->username }}</th>
                            <th>{{ $member->email }}</th>
                            <th>{{ $member->phone }}</th>
                            <th>{{ $member->enable }}</th>
                            <th>{{ $member->administrator }}</th>
                            <th>{{ $member->email_verified_at }}</th>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </x-pagination>
        </div>
    </main>
@endsection

