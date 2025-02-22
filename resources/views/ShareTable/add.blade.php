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
         * @var $newFiles \App\Models\VirtualFile[]
         */
        $newFiles=[];
        $shareTableName = "";
        $shareTableDescription = "";
        $shareTableType = "";
        $shareMembers = "";
        $popup = false;
        if(!empty($moreParams)){
            if(isset($moreParams[0]['files'])){
                $newFiles = $moreParams[0]['files'];
            }
            if(isset($moreParams[0]['popup'])){
                $popup = $moreParams[0]['popup'];
            }
            if(isset($moreParams[0]['value'])){
                $shareTableId = $moreParams[0]['value']["shareTableId"];
                $shareTableType = $moreParams[0]['value']["shareTableType"];
                $shareTableName = $moreParams[0]['value']["shareTableName"];
                $shareTableDescription = $moreParams[0]['value']["shareTableDescription"];
                $shareMembers = $moreParams[0]['value']["shareMembers"];
            }
        }
        if($popup){
            $menu=false;
            $footer=false;
        } else {
            $menu=true;
            $footer=true;
        }
@endphp
@vite(['resources/css/index.css', 'resources/js/index.js'])
@extends('layouts.default')
@section('title', "新增分享內容 | ".Config::get('app.name'))
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
    {{--@dump($request)--}}
    <x-scroll-indicator class="!bottom-[1px]" indicator-target="body"></x-scroll-indicator>
    <main>
        <div class="container2">
            @if(isset($moreParams[0]['value']))
            <form class="form-ct w-full"
                  data-fn="ShareTable.edit"
                  data-tracks="files*,shareTableName,shareTableType,shareTableDescription,password,password_confirmation,addFile,shareMembers,#filelabel,current_password,shareTableId"
                  data-target="#alert"
                  data-token="{{(new CSRF(RouteNameField::APIShareTableItemEditPost->value))->get()}}"
                  action="{{ route(RouteNameField::APIShareTableItemEditPost->value, ['id'=>$shareTableId]) }}"
                  method="post">
                <input type="file" class="filepond w-1/2"
                       data-allowtypes="image/png::image/jpg::image/jpeg::image/svg+xml::image/gif::image/webp::image/apng::image/bmp::image/avif::video/av1::video/H264::video/H264-SVC::video/H264-RCDO::video/H265::video/JPEG::video/JPEG::video/mpeg::video/mpeg4-generic::video/ogg::video/quicktime::video/JPEG::video/vnd.mpegurl::video/vnd.youtube.yt::video/VP8::video/VP9::video/mp4::video/mp4V-ES::video/MPV::video/vnd.directv.mpeg::video/vnd.dece.mp4::video/vnd.uvvu.mp4::video/H266::video/H263::video/H263-1998::video/H263-2000::video/H261::application/zip::application/x-zip-compressed::multipart/x-zip::application/x-compressed"
                       data-upload="{{ route(RouteNameField::APIShareTableItemUploadImage->value) }}"
                       data-revert="{{ route(RouteNameField::APIShareTableItemUploadImageRevert->value) }}"
                       data-patch="{{ route(RouteNameField::APIShareTableItemUploadImagePatch->value, ["fileinfo"=>" "]) }}"
                       data-multiple="true"
                       name="ItemImages[]"/>
            @else
            <form class="form-ct w-full"
                  data-fn="ShareTable.add"
                  data-tracks="files*,shareTableName,shareTableType,shareTableDescription,shareTableShortCode,password,password_confirmation,addFile,shareMembers,#filelabel"
                  data-target="#alert"
                  data-token="{{(new CSRF(RouteNameField::APIShareTableItemCreatePost->value))->get()}}"
                  action="{{ route(RouteNameField::APIShareTableItemCreatePost->value) }}"
                  method="post">
            @endif
                <div id="filelabel" class="tippyer text-2xl !border-0" data-placement="auto" data-trigger="manual" data-theme="light" data-zindex="19" data-htmlable="true" data-content="<li class='flex flex-nowrap'>⭕必填項目</li><li class='flex flex-nowrap'>❌必須有 1 個檔案</li>">檔案</div>
                <div class="file-driver">
                @foreach($newFiles as $file)
                    @if(isset($moreParams[0]['value']))
                        <div class="fd-item">
                            <input type="hidden" name="files[]" value="{{ $file->uuid }}">
                            <div class="fdi-preview">
                                @if(Utilsv2::isSupportImageFile($file->minetypes))
                                    <div class="placeholder w-[100%] h-[100%] bg-center bg-cover bg-no-repeat lazyImg" data-src="{{ $file->getTemporaryUrl(null, $shareTableId) }}" alt="{{ $file->filename }}"></div>
                                @elseif(Utilsv2::isSupportVideoFile($file->minetypes))
                                    <video controls src="{{ $file->getTemporaryUrl(null, $shareTableId) }}"></video>
                                @endif
                            </div>
                            <div class="fdi-delete btn btn-circle btn-color7 btn-border-0"><i class="fa-solid fa-xmark"></i></div>
                        </div>
                    @else
                        <div class="fd-item">
                            <input type="hidden" name="files[]" value="{{ $file->uuid }}">
                            <div class="fdi-preview overflow-hidden">
                                @if(Utilsv2::isSupportImageFile($file->minetypes))
                                    <img class="fdi-imginfo" src="{{ $file->getTemporaryUrl() }}" alt="{{ $file->filename }}">
                                @elseif(Utilsv2::isSupportVideoFile($file->minetypes))
                                    <video controls src="{{ $file->getTemporaryUrl() }}"></video>
                                @endif
                            </div>
                            <div class="fdi-delete btn btn-circle btn-color7 btn-border-0"><i class="fa-solid fa-xmark"></i></div>
                        </div>
                    @endif
                @endforeach
                </div>
                <div class="share-tables-form">
                    <div class="fdi-content">
                        <div id="alert"></div>
                        @if(isset($moreParams[0]['value']))
                            <input type="hidden" value="{{ $shareTableId }}" name="shareTableId">
                            <input type="submit" name="addFile" value="編輯檔案" class="btn btn-ripple btn-color7">
                        @else
                            <input type="submit" name="addFile" value="建立檔案" class="btn btn-ripple btn-color7">
                        @endif
                        <div class="fdic-field">
                            <label for="shareTableName">分享名稱<span class="text-red-500">*</span></label>
                            <input class="form-solid validate tippyer" data-placement="auto" data-trigger="manual" data-theme="light" data-zindex="19" data-htmlable="true" data-content="<li class='flex flex-nowrap'>⭕必填項目</li><li class='flex flex-nowrap'>❌最大的長度為255</li>" data-method="required" type="text" name="shareTableName" value="{{ $shareTableName }}" maxlength="255">
                        </div>
                        <div class="fdic-field">
                            <label for="shareTableDescription">說明</label>
                            <textarea class="form-solid" name="shareTableDescription">{{ $shareTableDescription }}</textarea>
                        </div>
                        <div class="fdic-field">
                            <label for="shareTableType">類型</label>
                            <select class="tom-select w-2/3" data-width="66%" name="shareTableType" required>
                                <option value="public" {{ $shareTableType === "public" ? "selected" : "" }}>公開</option>
                                <option value="private" {{ $shareTableType === "private" ? "selected" : "" }}>私人</option>
                            </select>
                        </div>
                        @if(!isset($moreParams[0]['value']))
                        <div class="fdic-field">
                            <label for="shareTableShortCode">分享代碼</label>
                            <input class="form-solid validate tippyer"
                                   data-placement="auto" data-trigger="manual" data-theme="light"
                                   data-zindex="19" data-htmlable="true"
                                   data-content="<li class='flex flex-nowrap'>❌最大的長度為255</li>" data-method="default" type="text" name="shareTableShortCode" maxlength="255">
                        </div>
                        @endif
                        <div class="fdic-field">
                            <label for="shareMembers">分享使用者</label>
                            <select class="tom-select w-2/3" data-src="{{ route(RouteNameField::APIGetUsers->value) }}" data-width="66%" name="shareMembers" multiple>
                                @php
                                    /**
                                     * @var \App\Models\Member[] $shareMembers
                                     */
                                @endphp
                                @if(isset($moreParams[0]['value']))
                                    @foreach($shareMembers as $value)
                                         <option value="{{ $value->id }}" selected>{{ $value->username }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        @if(isset($moreParams[0]['value']))
                        <div class="fdic-field">
                            <label for="password">現在分享密碼</label>
                            <div class="form-password-group w-2/3">
                                <input id="password3" class="block form-solid front !w-full validate tippyer"
                                       data-placement="auto" data-trigger="manual" data-theme="light"
                                       data-zindex="19" data-htmlable="true"
                                       data-content="<li class='flex flex-nowrap'>⭕必填項目</li><li class='flex flex-nowrap'>🌟獨一無二密碼</li><li class='flex flex-nowrap'>❌最小的長度為8</li>"
                                       data-method="default" type="password" maxlength="255"
                                       name="current_password" autocomplete="off">
                                <div class="btn btn-ripple btn-color7 btn-border-0 back ct"
                                     data-fn="password-toggle"
                                     data-target="#password3"><i class="fa-regular fa-eye"></i></div>
                            </div>
                        </div>
                        @endif
                        <div class="fdic-field">
                            <label for="password">新分享密碼</label>
                            <div class="form-password-group w-2/3">
                                <input id="password" class="block form-solid front !w-full validate tippyer"
                                       data-placement="auto" data-trigger="manual" data-theme="light"
                                       data-zindex="19" data-htmlable="true"
                                       data-content="<li class='flex flex-nowrap'>⭕必填項目</li><li class='flex flex-nowrap'>🌟獨一無二密碼</li><li class='flex flex-nowrap'>❌最小的長度為8</li>"
                                       data-method="default" type="password" maxlength="255"
                                       name="password" autocomplete="new-password">
                                <div class="btn btn-ripple btn-color7 btn-border-0 back ct"
                                     data-fn="password-toggle"
                                     data-target="#password"><i class="fa-regular fa-eye"></i></div>
                            </div>
                        </div>
                        <div class="fdic-field">
                            <label for="password_confirmation">確認新分享密碼</label>
                            <div class="form-password-group w-2/3">
                                <input id="password_confirmation"
                                       class="block form-solid front !w-full validate tippyer"
                                       data-placement="auto" data-trigger="manual" data-theme="light"
                                       data-zindex="19" data-htmlable="true"
                                       data-content="<li class='flex flex-nowrap'>⭕必填項目</li><li class='flex flex-nowrap'>🌟確認密碼</li><li class='flex flex-nowrap'>❌最小的長度為8</li>"
                                       data-method="default" type="password"
                                       name="password_confirmation" autocomplete="new-password">
                                <div class="btn btn-ripple btn-color7 btn-border-0 back ct"
                                     data-fn="password-toggle"
                                     data-target="#password_confirmation"><i class="fa-regular fa-eye"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>
@endsection
