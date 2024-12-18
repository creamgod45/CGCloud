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
        @php
            $popoverid = "popover_".\Illuminate\Support\Str::random(5);
            $popoverOptions = new PopoverOptions()@endphp
        <div class="container1">
            <x-popover-windows :id="$popoverid" :popover-options="$popoverOptions" popover-title="預覽分享資訊" class="shop-popover !hidden">
                <div class="shop-popover-placeholder placeholder placeholder-full-wh">
                    <div class="shop-popover-loader" role="status">
                        <svg aria-hidden="true"
                             class="inline w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-red-600"
                             viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                fill="currentColor"/>
                            <path
                                d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                fill="currentFill"/>
                        </svg>
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <iframe class="custom-page-iframe"></iframe>
            </x-popover-windows>
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
                @if(!empty($shareTables))
                    @foreach($shareTables as $shareTable)
                        @if($shareTable instanceof \App\Models\ShareTable)
                            @guest
                                <div class="panel-field-card vertical">
                                    <div class="pfc-icon"><i class="fa-solid fa-file"></i></div>
                                    <div class="pfc-title tippyer" data-placement="bottom" data-htmlable="true" data-content="登入了解更多"><i class="fa-solid fa-circle-info"></i> {{ $shareTable->name }}
                                    </div>
                                    <div class="pfc-preview">
                                        <img class="fdi-imginfo" src="{{ asset('assets/images/hidden.webp') }}" alt="登入了解更多">
                                    </div>
                                    <div class="pfc-operator">
                                        <div class="btn-md btn-border-0 btn btn-ripple btn-color2 tippyer" data-placement="bottom" data-content="登入解鎖操作"><i class="fa-solid fa-lock"></i></div>
                                    </div>
                                </div>
                            @endguest
                            @auth
                                <div class="panel-field-card vertical">
                                    @php
                                        $virtualFiles = $shareTable->getAllVirtualFiles();
                                        $id = "PFC_".\Illuminate\Support\Str::random(5);
                                    @endphp
                                    <div class="pfc-icon ct" data-fn="popover3" data-source="{{ $shareTable->id }}" data-target="#{{ $popoverid }}"><i class="fa-solid fa-file"></i></div>
                                    <div class="pfc-title tippyer" data-placement="bottom" data-htmlable="true"
                                         data-content="#{{ $id }}"><i
                                            class="fa-solid fa-circle-info"></i> {{ $shareTable->name }}
                                    </div>
                                    <div id="{{ $id }}" class="pfc-fileinfo !hidden">
                                        {{ $shareTable->description }}
                                        @foreach($virtualFiles as $virtualFile)
                                            <div class="pfc-fileinfo-item">
                                                <div class="pfcf-text">檔案名稱：{{ $virtualFile->filename }}</div>
                                                <div class="pfcf-text">
                                                    檔案大小：{{ \App\Lib\Utils\Utils::convertByte($virtualFile->size) }}</div>
                                                <div class="pfcf-text">檔案類型：{{ $virtualFile->minetypes }}</div>
                                                <div class="pfcf-text">建立日期：{{ $virtualFile->created_at }}</div>
                                                <div class="pfcf-text">過期日期：{{ $virtualFile->expired_at }}</div>
                                                <div class="pfcf-text">
                                                    擁有者：{{ $virtualFile->members()->first()->username }}</div>
                                                <div class="pfcf-text">
                                                    預覽網址：
                                                    <a target="_blank" rel="noreferrer noopener" href="{{ $virtualFile->getTemporaryUrl(now()->addMinutes(10), $shareTable->id) }}">傳送門</a>
                                                </div>
                                                <div class="pfcf-text">
                                                    下載網址：
                                                    <a target="_blank" rel="noreferrer noopener" href="{{ route(RouteNameField::PageShareTableItemDownload->value, ['id'=>$shareTable->id,"fileId"=> $virtualFile->uuid ]) }}">傳送門</a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="pfc-preview">
                                        @if($virtualFiles[0] !== null || !empty($virtualFiles))
                                            @if(Utilsv2::isSupportImageFile($virtualFiles[0]->minetypes))
                                                <img class="fdi-imginfo" src="{{ $virtualFiles[0]->getTemporaryUrl(now()->addMinutes(10), $shareTable->id) }}" alt="{{ $virtualFiles[0]->filename }}">
                                            @elseif(Utilsv2::isSupportVideoFile($virtualFiles[0]->minetypes) && $virtualFiles[0]->size <= 150 * 1024 * 1024)
                                                <video class="vjs video-js vjs-theme-forest"
                                                       data-minetype="{{ $virtualFiles[0]->minetypes }}" controls
                                                       data-src="{{ $virtualFiles[0]->getTemporaryUrl(now()->addMinutes(10), $shareTable->id) }}"></video>

                                            @elseif(Utilsv2::isSupportVideoFile($virtualFiles[0]->minetypes))
                                                <img class="fdi-imginfo tippyer" data-content="{{ $i18N->getLanguage(ELanguageText::FileSizeTooLarge) }}" src="{{ asset('assets/images/warning_file_size_large.webp') }}" alt="{{ $virtualFiles[0]->filename }}">
                                            @endif
                                        @endif
                                        {{--<video class="dashvideo" data-src="{{ asset('videos/Csgo331/Csgo331.mpd') }}" controls></video> --}}
                                        <div class="vjs-playlist"></div>
                                    </div>
                                    <div class="pfc-operator">
                                        <div class="btn-group">
                                            <div class="btn-md btn-border-0 btn btn-ripple btn-color2 tippyer"
                                                 data-placement="auto"
                                                 data-content="分享給"><i class="fa-solid fa-share"></i></div>
                                            <div class="btn-md btn-border-0 btn btn-ripple btn-ok tippyer" data-placement="auto"
                                                 data-content="複製"><i class="fa-solid fa-link"></i></div>
                                            <div class="btn-md btn-border-0 btn btn-ripple btn-color7 tippyer"
                                                 data-placement="auto"
                                                 data-content="下載"><i class="fa-solid fa-download"></i></div>
                                            <div class="btn-md btn-border-0 btn btn-ripple btn-warning tippyer"
                                                 data-placement="auto"
                                                 data-content="編輯"><i class="fa-solid fa-pen-to-square"></i></div>
                                            <div class="btn-md btn-border-0 btn btn-ripple btn-error tippyer last"
                                                 data-placement="auto"
                                                 data-content="刪除"><i class="fa-solid fa-trash"></i></div>
                                        </div>
                                    </div>
                                </div>
                            @endauth
                        @endif
                    @endforeach
                @endif
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
