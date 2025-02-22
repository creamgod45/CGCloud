@vite(['resources/css/index.css', 'resources/js/index.js'])
@use (App\Lib\I18N\ELanguageText;use App\Lib\I18N\I18N;use App\Lib\Utils\Htmlv2;use App\Lib\Utils\Utils;use App\Lib\Utils\Utilsv2;use App\View\Components\PopoverOptions;use Illuminate\Http\Request;use Illuminate\Pagination\LengthAwarePaginator;use Illuminate\Support\Facades\Config; use Nette\Utils\Json;use App\Lib\Utils\RouteNameField)
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
    <meta name="twitter:image" content="{{ asset("favicon.png") }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ Config::get("app.name") }}">
    <meta property="og:url" content="{{ Config::get("app.url") }}">
    <meta property="og:image" content="{{ asset("favicon.png") }}">
    <meta property="og:image:width" content="128">
    <meta property="og:image:height" content="128">
    <meta property="og:description" content="{{ Config::get("app.description") }}">
@endsection
@section('content')
    <x-scroll-indicator indicator-target="body"></x-scroll-indicator>
    <main>
        @php
            $popoverid = "popover_index";
            $popoverOptions = new PopoverOptions()@endphp
        <div class="container1">
            <x-popover-windows :id="$popoverid" :popover-options="$popoverOptions" popover-title="預覽分享資訊"
                               class="shop-popover !hidden">
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
                                <div class="panel-field-card vertical placeholder-ct placeholder"
                                     data-placeholderdelay="3000">
                                    <div class="pfc-icon"><i class="fa-solid fa-file"></i></div>
                                    <div class="pfc-title tippyer" data-placement="bottom" data-htmlable="true"
                                         data-content="登入了解更多"><i
                                            class="fa-solid fa-circle-info"></i> {{ $shareTable->name }}
                                    </div>
                                    <div class="pfc-preview">
                                        <img class="fdi-imginfo presize" data-prewidth="100%" data-preheight="300px"
                                             src="{{ asset('assets/images/hidden.webp') }}" alt="登入了解更多">
                                    </div>
                                    <div class="pfc-operator">
                                        <div class="btn-md btn-border-0 btn btn-ripple btn-color2 tippyer"
                                             data-placement="bottom" data-content="登入解鎖操作"><i
                                                class="fa-solid fa-lock"></i></div>
                                    </div>
                                </div>
                            @endguest
                            @auth
                                <div class="panel-field-card vertical placeholder-ct placeholder"
                                     data-placeholderdelay="3000">
                                    @php
                                        /**
                                         * @var \App\Models\VirtualFile[]|Illuminate\Database\Eloquent\Collection $virtualFiles
                                         **/
                                        $virtualFiles = $shareTable->getAllVirtualFiles();
                                        $id = "PFC_".\Illuminate\Support\Str::random(5);
                                    @endphp
                                    <div class="pfc-icon ct" data-fn="popover3" data-source="{{ $shareTable->id }}"
                                         data-target="#{{ $popoverid }}"><i class="fa-solid fa-file"></i></div>
                                    <div class="pfc-title tippyer" data-placement="bottom" data-htmlable="true"
                                         data-content="#{{ $id }}"><i
                                            class="fa-solid fa-circle-info"></i> {{ $shareTable->name }}
                                    </div>
                                    <div id="{{ $id }}" class="pfc-fileinfo !hidden">
                                        {{ $shareTable->description }}
                                        <div class="pfc-shell">
                                            @foreach($virtualFiles as $virtualFile)
                                                <div class="pfc-fileinfo-item">
                                                    <div class="pfcf-text"
                                                         data-filename="true">檔案名稱：
                                                         <span class="pfcf-text-filename" title="{{ $virtualFile->filename }}">{{ $virtualFile->filename }}</span>
                                                    </div>
                                                    <div class="pfcf-text">
                                                        檔案大小：{{ \App\Lib\Utils\Utils::convertByte($virtualFile->size) }}</div>
                                                    <div class="pfcf-text">檔案類型：{{ $virtualFile->minetypes }}</div>
                                                    <div class="pfcf-text">建立日期：{{ $virtualFile->created_at }}</div>
                                                    <div class="pfcf-text">過期日期：{{ $virtualFile->expired_at }}</div>
                                                    <div class="pfcf-text">
                                                        擁有者：{{ $virtualFile->members()->first()->username }}</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="pfc-preview">
                                        @if($virtualFiles->first() !== null)
                                            @if($virtualFiles->first() !== null || !empty($virtualFiles))
                                                @if(Utilsv2::isSupportImageFile($virtualFiles->first()->minetypes))
                                                    @php
                                                        // 獲取圖片物件
                                                        $image = $virtualFiles->first()->getImage($shareTable->id);

                                                        // 獲取圖片的原始寬度與高度
                                                        $width = $image->getWidth();
                                                        $height = $image->getHeight();

                                                        // 初始化縮放寬度與高度的變數
                                                        $scaledWidth = 0;
                                                        $scaledHeight = 0;

                                                        // 確保寬高值皆有效，避免計算錯誤
                                                        if ($width > 0 && $height > 0) {
                                                            if ($height > 300) {
                                                                // 如果原始高度大於 300，等比例縮小到高度 300 並調整寬度
                                                                $scaledHeight = 180;
                                                                $scaledWidth = ($width / $height) * $scaledHeight;
                                                            } else {
                                                                // 如果原始高度小於等於 300，放大高度到 300 並調整寬度
                                                                $scaledHeight = 180;
                                                                $scaledWidth = ($width / $height) * $scaledHeight;
                                                            }
                                                        } else {
                                                            // 如果寬高無效，設定為預設值 0 以便後續檢查
                                                            $scaledWidth = 0;
                                                            $scaledHeight = 0;
                                                        }
                                                    @endphp
                                                    <img class="fdi-imginfo presize" loading="lazy"
                                                         data-prewidth="{{ $scaledWidth }}px"
                                                         data-preheight="{{ $scaledHeight }}px"
                                                         src="{{ $virtualFiles->first()->getTemporaryUrl(now()->addMinutes(10), $shareTable->id) }}"
                                                         alt="{{ $virtualFiles->first()->filename }}">
                                                @elseif(Utilsv2::isSupportVideoFile($virtualFiles->first()->minetypes) && $virtualFiles->first()->size <= 150 * 1024 * 1024)
                                                    @php
                                                        /** @var \App\Models\ShareTableVirtualFile[]|\Illuminate\Database\Eloquent\Collection<\App\Models\ShareTableVirtualFile> $shareTableVirtualFiles */
                                                        $shareTableVirtualFiles = $shareTable->shareTableVirtualFile()->getResults();
                                                        $ftype = 'data-minetype='.$virtualFiles->first()->minetypes;
                                                        $f = $virtualFiles->first()->getTemporaryUrl(now()->addMinutes(10), $shareTable->id);
                                                        if($shareTableVirtualFiles !== null){
                                                            foreach ($shareTableVirtualFiles as $item) {
                                                                if($item->virtual_file_uuid === $virtualFiles->first()->uuid && $item->isAvailableDashVideo()){
                                                                    /** @var \App\Models\DashVideos $dashVideos */
                                                                    $dashVideos = \App\Models\DashVideos::where('virtual_file_uuid', '=', $virtualFiles->first()->uuid)->get()->first();
                                                                    if($dashVideos !== null){
                                                                        $ftype = 'data-type=dash';
                                                                        $f = route(RouteNameField::APIPreviewFileDash->value, [
                                                                            'shareTableId' => $shareTable->id,
                                                                            'fileId' => $virtualFiles->first()->uuid,
                                                                            'fileName' => $dashVideos->filename.".".$dashVideos->extension,
                                                                        ]);
                                                                    }
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                    @endphp
                                                    <video class="vjs video-js vjs-theme-forest presize" {{ $ftype }} controls data-src="{{ $f }}"></video>
                                                @elseif(Utilsv2::isSupportVideoFile($virtualFiles->first()->minetypes))
                                                    @php
                                                        /** @var \App\Models\ShareTableVirtualFile[]|\Illuminate\Database\Eloquent\Collection<\App\Models\ShareTableVirtualFile> $shareTableVirtualFiles */
                                                        $shareTableVirtualFiles = $shareTable->shareTableVirtualFile()->getResults();
                                                        $ftype = 'data-minetype='.$virtualFiles->first()->minetypes;
                                                        $f = $virtualFiles->first()->getTemporaryUrl(now()->addMinutes(10), $shareTable->id);
                                                        if($shareTableVirtualFiles !== null){
                                                            $no = true;
                                                            foreach ($shareTableVirtualFiles as $item) {
                                                                if($item->virtual_file_uuid === $virtualFiles->first()->uuid && $item->isAvailableDashVideo()){
                                                                    /** @var \App\Models\DashVideos $dashVideos */
                                                                    $dashVideos = \App\Models\DashVideos::where('virtual_file_uuid', '=', $virtualFiles->first()->uuid)->get()->first();
                                                                    if($dashVideos !== null){
                                                                        $no = false;
                                                    @endphp
                                                                        <video class="vjs video-js vjs-theme-forest presize" data-type="dash" controls data-src="{{ route(RouteNameField::APIPreviewFileDash->value, ['shareTableId' => $shareTable->id,'fileId' => $virtualFiles->first()->uuid,'fileName' => $dashVideos->filename.".".$dashVideos->extension]) }}"></video>
                                                    @php
                                                                    }
                                                                    break;
                                                                }
                                                            }
                                                            if($no) {
                                                    @endphp
                                                            <img class="fdi-imginfo tippyer presize" loading="lazy"
                                                                 data-prewidth="100%"
                                                                 data-preheight="300px"
                                                                 data-content="{{ $i18N->getLanguage(ELanguageText::FileSizeTooLarge) }}"
                                                                 src="{{ asset('assets/images/warning_file_size_large.webp') }}"
                                                                 alt="{{ $virtualFiles->first()->filename }}">
                                                    @php
                                                            }
                                                        }
                                                    @endphp
                                                @endif
                                            @endif
                                        @endif
                                        {{--<video class="dashvideo" data-src="{{ asset('videos/Csgo331/Csgo331.mpd') }}" controls></video> --}}
                                        <div class="vjs-playlist"></div>
                                    </div>
                                    <div class="pfc-operator">
                                        <div class="btn-group">
                                            @php
                                                $random = $shareTable->id;
                                                $url = $shareTable->shareURL();
                                                $v = $virtualFiles->setVisible(['id','uuid', 'filename', 'size', 'created_at'])->toArray();
                                                foreach ($v as $key => $item) {
                                                    /** @var \App\Models\VirtualFile $item */
                                                    $v[$key]['size'] = Utils::convertByte($item['size']);
                                                    $aUrl = route(RouteNameField::APIShareTableItemConversion->value, ['id' => $random, 'fileId' => $item['uuid']]);
                                                    /** @var \App\Models\DashVideos $dashVideo */
                                                    $dashVideo = \App\Models\DashVideos::where('virtual_file_uuid', '=', $item['uuid'])->get()->first();
                                                    if($dashVideo !== null){
                                                        $btn = "";
                                                        switch ($dashVideo->type){
                                                            case "failed":
                                                                $btn = '<a data-fn="shareable_conversion_file" data-type="error" data-parent="#conversion_'.$random.'" data-title="是否確認轉換此檔案?" data-id="#conversion_item_'.$key.'" data-confirmboxcontent="此操作將會轉換成檔案" data-href="'.$aUrl.'" class="btn-md btn-border-0 btn btn-ripple btn-error confirm-box"><i class="fa-solid fa-industry"></i>&nbsp;轉換檔案</a>';
                                                                break;
                                                        }
                                                    } else {
                                                        $btn = '<a data-fn="shareable_conversion_file" data-type="error" data-parent="#conversion_'.$random.'" data-title="是否確認轉換此檔案?" data-id="#conversion_item_'.$key.'" data-confirmboxcontent="此操作將會轉換成檔案" data-href="'.$aUrl.'" class="btn-md btn-border-0 btn btn-ripple btn-error confirm-box"><i class="fa-solid fa-industry"></i>&nbsp;轉換檔案</a>';
                                                    }
                                                    $v[$key]['action'] = '<div class="flex gap-3"><div id="conversion_item_'.$key.'" class="autoupdate" data-fn="get_dash_progress" data-id="'.$item['uuid'].'" ></div>'.$btn.'</div>';
                                                }
                                            @endphp
                                            @if($shareTable->member_id === \Illuminate\Support\Facades\Auth::user()->id)
                                                <a data-id="{{ $random }}"
                                                   data-type="conversion"
                                                   data-href="{{ route(RouteNameField::APIShareTableItemConversion->value, ['id' => $random, 'fileId' => "%fileId%" ]) }}"
                                                   data-data="{{ json_encode($v) }}"
                                                   popovertarget="{{ "conversion_".$random }}"
                                                   class="btn-md btn-border-0 btn btn-ripple btn-color3 shareable tippyer"
                                                   data-placement="bottom"
                                                   data-content="轉換"><i class="fa-solid fa-file-export"></i></a>
                                                <a data-href="{{ $url }}"
                                                   data-id="{{ $random }}"
                                                   data-type="share"
                                                   data-user="{{ route(RouteNameField::APIGetUsers->value) }}"
                                                   popovertarget="{{ "shareable_".$random }}"
                                                   class="btn-md btn-border-0 btn btn-ripple btn-color2 shareable tippyer"
                                                   data-placement="bottom"
                                                   data-content="分享給"><i class="fa-solid fa-share"></i></a>
                                            @endif
                                            <div class="btn-md btn-border-0 btn btn-ripple btn-ok tippyer copyer"
                                                 data-url="{{ $url }}"
                                                 data-placement="bottom"
                                                 data-content="複製"><i class="fa-solid fa-link"></i></div>
                                            @php
                                                $random1 = $shareTable->id;
                                                $url = $shareTable->shareURL();
                                                $v = $virtualFiles->setVisible(['id','uuid', 'filename', 'size', 'created_at'])->toArray();
                                                foreach ($v as $key => $item) {
                                                    $v[$key]['size'] = Utils::convertByte($item['size']);
                                                    $dashVideo = \App\Models\DashVideos::where('virtual_file_uuid', '=', $item['uuid'])->get()->first();
                                                    $dashVideoBtn = "";
                                                    if($dashVideo !== null){
                                                        $url = route(RouteNameField::PagePreviewFilePlayerDash->value, [
                                                            'shareTableId' => $shareTable->id,
                                                            'fileId' => $item['uuid'],
                                                            'fileName' => $dashVideo->filename.".".$dashVideo->extension,
                                                        ]);
                                                        $dashVideoBtn = '<a target="_blank" rel="noreferrer noopener" href="'.$url.'" class="btn-md btn-border-0 btn btn-ripple btn-warning"><i class="fa-solid fa-eye"></i>&nbsp;線上串流預覽</a>';
                                                    }
                                                    $v[$key]['action'] = '<div class="flex gap-3">'.$dashVideoBtn.'<a target="_blank" rel="noreferrer noopener" href="%url-0%" class="btn-md btn-border-0 btn btn-ripple btn-warning"><i class="fa-solid fa-eye"></i>&nbsp;預覽</a><a href="%url-1%" class="btn-md btn-border-0 btn btn-ripple btn-color7"><i class="fa-solid fa-download"></i>&nbsp;下載</a><a data-fn="shareable_delete_file" data-type="error" data-parent="#download_'.$random1.'" data-title="是否確認刪除此檔案?" data-confirmboxcontent="此操作將會永遠的刪除!!" data-href="%url-2%" class="btn-md btn-border-0 btn btn-ripple btn-error confirm-box"><i class="fa-solid fa-trash"></i>&nbsp;刪除</a></div>';
                                                }
                                            @endphp
                                            <div data-id="{{ $random1 }}"
                                                 data-type="download"
                                                 data-href="{{ route(RouteNameField::PageShareTableItemDownload->value, ['id'=> $shareTable->id,"fileId"=> "%fileId%" ]) }}"
                                                 data-delete="{{ route(RouteNameField::PageShareTableItemDelete->value, ['id'=> $shareTable->id,"fileId"=> "%fileId%" ]) }}"
                                                 data-data="{{ json_encode($v) }}"
                                                 popovertarget="{{ "download_".$random1 }}"
                                                 class="btn-md btn-border-0 btn btn-ripple btn-color7 shareable tippyer"
                                                 data-placement="bottom"
                                                 data-content="下載"><i class="fa-solid fa-download"></i></div>
                                            @if($shareTable->member_id === \Illuminate\Support\Facades\Auth::user()->id)
                                                <div class="btn-md btn-border-0 btn btn-ripple btn-warning tippyer ct"
                                                     data-fn="popover4" data-source="{{ $shareTable->id }}"
                                                     data-target="#{{ $popoverid }}"
                                                     data-placement="bottom"
                                                     data-content="編輯"><i class="fa-solid fa-pen-to-square"></i></div>
                                                <div
                                                    class="btn-md btn-border-0 btn btn-ripple btn-error tippyer last confirm-box"
                                                    data-fn="shareable_delete"
                                                    data-title="你確定要刪除此分享資源?"
                                                    data-confirmboxcontent="此操作將會永遠的刪除!!"
                                                    data-type="error"
                                                    data-href="{{ route(RouteNameField::PageShareTableDelete->value, ['id'=> $shareTable->id ]) }}"
                                                    data-placement="bottom"
                                                    data-content="刪除"><i class="fa-solid fa-trash"></i>
                                                </div>
                                            @endif
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
