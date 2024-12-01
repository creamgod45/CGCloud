@vite(['resources/css/index.css', 'resources/js/index.js'])
@use (App\Lib\I18N\ELanguageText;use App\Lib\I18N\I18N;use App\Lib\Utils\Htmlv2;use App\Lib\Utils\Utilsv2;use App\View\Components\PopoverOptions;use Illuminate\Http\Request;use Illuminate\Pagination\LengthAwarePaginator;use Illuminate\Support\Facades\Config; use Nette\Utils\Json;use App\Lib\Utils\RouteNameField)
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
    $shareTables = [];
    if(!empty($moreParams)){
        if(isset($moreParams[0]['$shareTables'])){
            $shareTables = $moreParams[0]['$shareTables'];
        }
    }
@endphp
@extends('layouts.default')
@section('title', Config::get("app.description")." | ".Config::get('app.name'))
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
        <div class="container1">
            <div class="panel-title-field gfont-noto-serif-tc-bold">檔案列表</div>
            @auth
            <form class="panel-upload-field" method="post"
                  action="{{ route(RouteNameField::PageShareTableItemPost->value)  }}">
                @csrf
                <input type="submit" class="btn btn-ripple btn-color7 btn-md-strip" name="upload" value="上傳">
                <input type="file" class="filepond w-1/2"
                       data-allowtypes="image/png::image/jpg::image/jpeg::image/svg+xml::image/gif::image/webp::image/apng::image/bmp::image/avif::video/av1::video/H264::video/H264-SVC::video/H264-RCDO::video/H265::video/JPEG::video/JPEG::video/mpeg::video/mpeg4-generic::video/ogg::video/quicktime::video/JPEG::video/vnd.mpegurl::video/vnd.youtube.yt::video/VP8::video/VP9::video/mp4::video/mp4V-ES::video/MPV::video/vnd.directv.mpeg::video/vnd.dece.mp4::video/vnd.uvvu.mp4::video/H266::video/H263::video/H263-1998::video/H263-2000::video/H261::application/zip::application/x-zip-compressed::multipart/x-zip::application/x-compressed"
                       data-upload="{{ route(RouteNameField::APIShareTableItemUploadImage->value) }}"
                       data-revert="{{ route(RouteNameField::APIShareTableItemUploadImageRevert->value) }}"
                       data-patch="{{ route(RouteNameField::APIShareTableItemUploadImagePatch->value, ["fileinfo"=>" "]) }}"
                       data-multiple="true"
                       name="ItemImages[]"/>
            </form>
            @endauth
            <div class="panel-field-list">
                @foreach($shareTables as $shareTable)
                    @if($shareTable instanceof \App\Models\ShareTable)
                        @php
                            $virtualFiles = $shareTable->getAllVirtualFiles();
                        @endphp
                        <div class="panel-field-card vertical">
                            <div class="pfc-icon"><i class="fa-solid fa-file"></i></div>
                            <div class="pfc-title tippyer" data-placement="auto" data-htmlable="true" data-content="#tipper2"><i
                                    class="fa-solid fa-circle-info"></i> {{ $shareTable->name }}
                            </div>
                            <div id="tipper2" class="pfc-fileinfo !hidden">
                                {{ $shareTable->description }}
                                @foreach($virtualFiles as $virtualFile)
                                    <div class="pfc-fileinfo-item">
                                        <div class="pfcf-text">檔案大小：</div>
                                        <div class="pfcf-text">檔案類型：{{ $virtualFile->minetypes }}</div>
                                        <div class="pfcf-text">建立日期：{{ $virtualFile->created_at }}</div>
                                        <div class="pfcf-text">過期日期：{{ $virtualFile->expired_at }}</div>
                                        <div class="pfcf-text">擁有者：{{ $virtualFile->members()->first()->username }}</div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="pfc-preview">
                                {{-- <video class="dashvideo" data-src="{{ asset('videos/Csgo331/Csgo331.mpd') }}" controls></video> --}}
                                {{--                        <video class="vjs video-js vjs-theme-forest" data-src="{{ asset('videos/Csgo331/Csgo331.mpd') }}" controls></video>--}}
                                <div class="vjs-playlist"></div>
                            </div>
                            <div class="pfc-operator">
                                <div class="btn-group">
                                    <div class="btn-md btn-border-0 btn btn-ripple btn-color2 tippyer" data-placement="auto"
                                         data-content="分享給"><i class="fa-solid fa-share"></i></div>
                                    <div class="btn-md btn-border-0 btn btn-ripple btn-ok tippyer" data-placement="auto"
                                         data-content="複製"><i class="fa-solid fa-link"></i></div>
                                    <div class="btn-md btn-border-0 btn btn-ripple btn-color7 tippyer" data-placement="auto"
                                         data-content="下載"><i class="fa-solid fa-download"></i></div>
                                    <div class="btn-md btn-border-0 btn btn-ripple btn-warning tippyer" data-placement="auto"
                                         data-content="編輯"><i class="fa-solid fa-pen-to-square"></i></div>
                                    <div class="btn-md btn-border-0 btn btn-ripple btn-error tippyer last" data-placement="auto"
                                         data-content="刪除"><i class="fa-solid fa-trash"></i></div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
               {{--
               <div class="panel-field-card vertical">
                    <div class="pfc-icon"><i class="fa-solid fa-file"></i></div>
                    <div class="pfc-title tippyer" data-placement="auto" data-htmlable="true" data-content="#tipper2"><i
                            class="fa-solid fa-circle-info"></i> 檔案
                    </div>
                    <div id="tipper2" class="pfc-fileinfo !hidden">
                        <div class="pfcf-text">檔案大小：</div>
                        <div class="pfcf-text">檔案類型：</div>
                        <div class="pfcf-text">建立日期：</div>
                        <div class="pfcf-text">過期日期：</div>
                        <div class="pfcf-text">擁有者：</div>
                    </div>
                    <div class="pfc-preview">
                        --}}{{-- <video class="dashvideo" data-src="{{ asset('videos/Csgo331/Csgo331.mpd') }}" controls></video> --}}{{--
                        --}}{{--                        <video class="vjs video-js vjs-theme-forest" data-src="{{ asset('videos/Csgo331/Csgo331.mpd') }}" controls></video>--}}{{--
                        <div class="vjs-playlist"></div>
                    </div>
                    <div class="pfc-operator">
                        <div class="btn-group">
                            <div class="btn-md btn-border-0 btn btn-ripple btn-color2 tippyer" data-placement="auto"
                                 data-content="分享給"><i class="fa-solid fa-share"></i></div>
                            <div class="btn-md btn-border-0 btn btn-ripple btn-ok tippyer" data-placement="auto"
                                 data-content="複製"><i class="fa-solid fa-link"></i></div>
                            <div class="btn-md btn-border-0 btn btn-ripple btn-color7 tippyer" data-placement="auto"
                                 data-content="下載"><i class="fa-solid fa-download"></i></div>
                            <div class="btn-md btn-border-0 btn btn-ripple btn-warning tippyer" data-placement="auto"
                                 data-content="編輯"><i class="fa-solid fa-pen-to-square"></i></div>
                            <div class="btn-md btn-border-0 btn btn-ripple btn-error tippyer last" data-placement="auto"
                                 data-content="刪除"><i class="fa-solid fa-trash"></i></div>
                        </div>
                    </div>
                </div>
                <div class="panel-field-card horizontal">
                    <div class="pfc-field-group">
                        <div class="pfc-icon tippyer !border-none" data-htmlable="true" data-placement="auto"
                             data-content="#tipper3"><i class="fa-regular fa-file-video"></i></div>
                        <div class="pfc-title">
                            <div class="tippyer w-fit" data-placement="auto" data-htmlable="true"
                                 data-content="#tipper1"><i class="fa-solid fa-circle-info"></i> 檔案
                            </div>
                        </div>
                    </div>
                    <div id="tipper1" class="pfc-fileinfo !hidden">
                        <div class="pfcf-text">檔案大小：</div>
                        <div class="pfcf-text">檔案類型：</div>
                        <div class="pfcf-text">建立日期：</div>
                        <div class="pfcf-text">過期日期：</div>
                        <div class="pfcf-text">擁有者：</div>
                    </div>
                    <div id="tipper3" class="pfc-preview !hidden">
                        --}}{{--                        <video onmouseenter="document.dispatchEvent(new CustomEvent('CG::Video', { detail: this}))" class="vjs video-js vjs-theme-forest" data-src="{{ asset('videos/Csgo331/Csgo331.mpd') }}" controls></video>--}}{{--
                        <div class="vjs-playlist"></div>
                    </div>
                    <div class="pfc-operator">
                        <div class="btn-group">
                            <div class="btn-md btn-border-0 btn btn-ripple btn-color2 tippyer" data-placement="auto"
                                 data-content="分享給"><i class="fa-solid fa-share"></i></div>
                            <div class="btn-md btn-border-0 btn btn-ripple btn-ok tippyer" data-placement="auto"
                                 data-content="複製"><i class="fa-solid fa-link"></i></div>
                            <div class="btn-md btn-border-0 btn btn-ripple btn-color7 tippyer" data-placement="auto"
                                 data-content="下載"><i class="fa-solid fa-download"></i></div>
                            <div class="btn-md btn-border-0 btn btn-ripple btn-warning tippyer" data-placement="auto"
                                 data-content="編輯"><i class="fa-solid fa-pen-to-square"></i></div>
                            <div class="btn-md btn-border-0 btn btn-ripple btn-error tippyer last" data-placement="auto"
                                 data-content="刪除"><i class="fa-solid fa-trash"></i></div>
                        </div>
                    </div>
                </div>--}}
            </div>
        </div>
    </main>
@endsection
