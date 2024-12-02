@vite(['resources/css/index.css', 'resources/js/index.js'])
@use (App\Lib\I18N\ELanguageText;use App\Lib\I18N\I18N;use App\Lib\Utils\Htmlv2;use App\Lib\Utils\Utilsv2;use App\View\Components\PopoverOptions;use Illuminate\Http\Request;use Illuminate\Pagination\LengthAwarePaginator;use Illuminate\Support\Facades\Config; use Nette\Utils\Json;use App\Lib\Utils\RouteNameField;use App\Lib\Server\CSRF)
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
    /**
     * @var $shareTable \App\Models\ShareTable
     * @var $sharePermissions \App\Models\SharePermissions[]|\Illuminate\Database\Eloquent\Collection
     * @var $virtualFiles \App\Models\VirtualFile[]|\Illuminate\Database\Eloquent\Collection
     */
    $menu=true;
    $footer=true;
    $shareTable = null;
    $virtualFiles=[];
    $sharePermissions=[];
    $popup = false;
    if(!empty($moreParams)){
        if(isset($moreParams[0]['shareTable'])){
            $shareTable = $moreParams[0]['shareTable'];
        }
        if(isset($moreParams[0]['virtualFiles'])){
            $virtualFiles = $moreParams[0]['virtualFiles'];
        }
        if(isset($moreParams[0]['sharePermissions'])){
            $sharePermissions = $moreParams[0]['sharePermissions'];
        }
        if(isset($moreParams[0]['popup'])){
            $popup = $moreParams[0]['popup'];
            if($popup){
                $menu=false;
                $footer=false;
            }
        }
    }
@endphp
@extends('layouts.default')
@section('title', "檢視 ".$shareTable->name." | ".Config::get('app.name'))
@section('head')
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ Config::get("app.name") }}">
    <meta name="twitter:site" content="{{ "@".Config::get("app.url") }}">
    <meta name="twitter:description" content="{{ Config::get("app.description") }}">
    <meta name="twitter:image" content="{{ asset("favicon.ico") }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ Config::get("app.name") }}">
    <meta property="og:url" content="{{ Config::get("app.url") }}">
    <meta property="og:image" content="{{ asset("favicon.ico") }}">
    <meta property="og:image:width" content="128">
    <meta property="og:image:height" content="128">
    <meta property="og:description" content="{{ Config::get("app.description") }}">
@endsection
@section('content')
    <x-scroll-indicator indicator-target="body"></x-scroll-indicator>
    <main>
        <div class="share-table-view">
            <div class="form-content-group">
                <div class="form-content-header">
                    分享資訊名稱
                </div>
                <div class="form-content-content">
                    <input class="form-solid w-1/2" readonly value="{{ $shareTable->name }}">
                </div>
            </div>
            <div class="form-content-group">
                <div class="form-content-header">
                    分享資訊說明
                </div>
                <div class="form-content-content">
                    <textarea class="form-solid w-1/2">{{ $shareTable->description }}</textarea>
                </div>
            </div>
            <div class="form-content-group">
                <div class="form-content-header">
                    短連接代碼
                </div>
                <div class="form-content-content">
                    <input class="form-solid w-1/2" readonly value="{{ $shareTable->short_code }}">
                </div>
            </div>
            <div class="form-content-group">
                <div class="form-content-header">
                    分享資訊可見度
                </div>
                <div class="form-content-content">
                    <select class="tom-select w-1/2" data-disabled="true" readonly data-width="50%">
                        <option selected>{{ $shareTable->type }}</option>
                    </select>
                </div>
            </div>
            <div class="form-content-group">
                <div class="form-content-header">
                    分享資訊指定授權用戶
                </div>
                <div class="form-content-content">
                    <ul>
                    @if($sharePermissions->isEmpty())
                        <li>{{ $i18N->getLanguage(ELanguageText::ShareTableItemViewNoAuthorizationUser) }}</li>
                    @endif
                    @foreach($sharePermissions as $sharePermission)
                        @php
                            $member = $sharePermission->member()->first()@endphp
                        <li>{{ $member->username }}</li>
                    @endforeach
                    </ul>
                </div>
            </div>
            @php
                $columns = [
                    [ "data" => 'id', "name" => "id", "title" => "ID", "footer" => "ID" ],
                    [ "data" => 'goto', "title" => "前往", "footer" => "前往", "className" => 'clickable', "orderable"=> false,"searchable"=> false, ],
                    [ "data" => 'filename', "name" => "filename", "title" => "名稱", "footer" => "名稱" ],
                    [ "data" => 'created_at', "name" => "created_at", "title" => "建立時間" , "footer" => "建立時間" ],
                    [ "data" => 'size', "name" => "size", "title" => "大小", "footer" => "大小" ],
                    [ "data" => 'action', "name" => "action", "title" => "操作", "footer" => "操作" ],
                ];
            @endphp
            <table class="datatable" data-cgdatatype="JSON" data-cgdata="{{ Json::encode($virtualFiles) }}" data-cgcolumns="{{ Json::encode($columns) }}" data-cgfixedtable="true">
            </table>
        </div>
    </main>
@endsection
