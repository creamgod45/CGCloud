@php 
    use App\Lib\Utils\RouteNameField;
    use App\Lib\Utils\Utils;
    use App\Lib\Utils\Utilsv2;
    use App\Lib\I18N\ELanguageText; 
    
    /** @var \App\Models\ShareTable $shareTable */
    /** @var \App\Models\VirtualFile[]|\Illuminate\Database\Eloquent\Collection $virtualFiles */
    $virtualFiles = $shareTable->getAllVirtualFiles();
    $user = \Illuminate\Support\Facades\Auth::user();
    
    // 計算權限與狀態
    $hasPassword = $shareTable->hasPassword();
    $sessionKey = \App\Http\Controllers\ShareTablePasswordController::sessionKey($shareTable->short_code);
    $unlockedAt = session()->get($sessionKey.'_at', 0);
    // 檢查是否已解鎖且在 60 秒效期內
    $isUnlocked = session()->has($sessionKey) && (now()->timestamp - $unlockedAt) <= 60;
    
    // 擁有者校驗：若有密碼，擁有者也必須在 1 分鐘解鎖效期內
    $showPasswordLock = $hasPassword && !$isUnlocked;
@endphp

@guest
    {{-- 原本設計的初衷：訪客佔位符 --}}
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
            <a href="{{ route(RouteNameField::PageLogin->value) }}" class="btn-md btn-border-0 btn btn-ripple btn-color2 tippyer"
                 data-placement="bottom" data-content="登入解鎖操作"><i
                    class="fa-solid fa-lock"></i></a>
        </div>
    </div>
@else
    {{-- 已登入使用者區塊 --}}
    @if($showPasswordLock)
        {{-- 【分支 22】受密碼保護：即使是擁有者也需要保護 --}}
        <div class="panel-field-card vertical placeholder-ct placeholder"
             data-placeholderdelay="3000">
            <div class="pfc-icon"><i class="fa-solid fa-lock text-yellow-500"></i></div>
            <div class="pfc-title tippyer" data-placement="bottom" data-htmlable="true"
                 data-content="受密碼保護內容"><i
                        class="fa-solid fa-circle-info"></i> {{ $shareTable->name }}
            </div>
            <div class="pfc-preview password-protected-trigger cursor-pointer"
                 data-shortcode="{{ $shareTable->short_code }}" 
                 data-title="{{ $shareTable->name }}"
                 data-token="{{ (new \App\Lib\Server\CSRF('sharetable.unlock'))->get() }}">
                <img class="fdi-imginfo presize opacity-60" data-prewidth="100%" data-preheight="300px"
                     src="{{ asset('assets/images/hidden.webp') }}" alt="受密碼保護內容">
            </div>
            <div class="pfc-operator">
                <div class="btn-md btn-border-0 btn btn-ripple btn-color2 tippyer password-protected-trigger"
                     data-shortcode="{{ $shareTable->short_code }}" 
                     data-title="{{ $shareTable->name }}"
                     data-placement="bottom" data-content="輸入密碼解鎖"><i
                            class="fa-solid fa-key"></i></div>
            </div>
        </div>
    @else
        {{-- 【分支 41】一般物件：完整渲染 --}}
        <div class="panel-field-card vertical placeholder-ct placeholder"
             data-placeholderdelay="3000" data-sharetableid="{{ $shareTable->id }}">
            @php
                $id = "PFC_".\Illuminate\Support\Str::random(5);
            @endphp
            <div aria-haspopup="dialog" role="link" class="pfc-icon ct" data-fn="popover3" data-source="{{ $shareTable->id }}"
                 data-target="#{{ $popoverid }}"><i class="fa-solid fa-file text-color7"></i></div>
            <div aria-description="{{ $shareTable->name }}" role="tooltip" class="pfc-title tippyer" data-placement="bottom" data-htmlable="true"
                 data-content="#{{ $id }}"><i
                        class="fa-solid fa-circle-info mr-1"></i> {{ $shareTable->name }}
            </div>
            <div id="{{ $id }}" class="pfc-fileinfo !hidden">
                {{ $shareTable->description }}
                <div class="pfc-shell">
                    @if($virtualFiles->isNotEmpty())
                        @foreach($virtualFiles as $virtualFile)
                            <div class="pfc-fileinfo-item">
                                <div class="pfcf-text"
                                     data-filename="true">檔案名稱：
                                    <span class="pfcf-text-filename"
                                          title="{{ $virtualFile->filename }}">{{ $virtualFile->filename }}</span>
                                </div>
                                <div class="pfcf-text">
                                    檔案大小：{{ \App\Lib\Utils\Utils::convertByte(($virtualFile->size > 0) ? $virtualFile->size : 1) }}</div>
                                <div class="pfcf-text">檔案類型：{{ $virtualFile->minetypes }}</div>
                                <div class="pfcf-text">建立日期：{{ $virtualFile->created_at }}</div>
                                <div class="pfcf-text">擁有者：{{ $virtualFile->members()?->first()?->username }}</div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
            <div class="pfc-preview">
                @if($virtualFiles->isNotEmpty() && $virtualFiles->first() !== null)
                    @if(Utilsv2::isSupportImageFile($virtualFiles->first()->minetypes))
                        @php
                            $image = $virtualFiles->first();
                            $width = 0; $height = 0;
                            $image1 = $image->getImage($shareTable->id);
                            if($image1 !== null){
                                $width = $image1->getWidth();
                                $height = $image1->getHeight();
                            }
                            $scaledHeight = 180;
                            $scaledWidth = ($width > 0 && $height > 0) ? ($width / $height) * $scaledHeight : 0;
                        @endphp
                        <img class="fdi-imginfo presize" loading="lazy"
                             data-prewidth="{{ $scaledWidth }}px"
                             data-preheight="{{ $scaledHeight }}px"
                             src="{{ $virtualFiles->first()->getTemporaryUrl(now()->addMinutes(10), $shareTable->id) }}"
                             alt="{{ $virtualFiles->first()->filename }}">
                    @elseif(Utilsv2::isSupportVideoFile($virtualFiles->first()->minetypes))
                        @php
                            $shareTableVirtualFiles = $shareTable->shareTableVirtualFile()->getResults();
                            $ftype = 'data-minetype='.$virtualFiles->first()->minetypes;
                            $f = $virtualFiles->first()->getTemporaryUrl(now()->addMinutes(10), $shareTable->id);
                            $poster = ""; $isDash = false;
                            if($shareTableVirtualFiles !== null){
                                foreach ($shareTableVirtualFiles as $item) {
                                    if($item->virtual_file_uuid === $virtualFiles->first()->uuid && $item->isAvailableDashVideo()){
                                        $dashVideos = \App\Models\DashVideos::where('virtual_file_uuid', '=', $virtualFiles->first()->uuid)->get()->first();
                                        if($dashVideos !== null){
                                            $isDash = true; $ftype = 'data-type=dash';
                                            $f = route(RouteNameField::APIPreviewFileDash->value, ['shareTableId' => $shareTable->id,'fileId' => $virtualFiles->first()->uuid,'fileName' => $dashVideos->filename.".".$dashVideos->extension]);
                                            $result = $dashVideos->thumbVirtualFile()->getResults();
                                            if($result) $poster = 'data-poster="'.$result->getThumbTemporaryUrl(now()->addMinutes(10)).'"';
                                        }
                                        break;
                                    }
                                }
                            }
                        @endphp
                        @if($isDash)
                            <video class="shaka-player presize" {!! $ftype !!} {!! $poster !!} controls data-src="{{ $f }}"></video>
                        @elseif($virtualFiles->first()->size <= 150 * 1024 * 1024)
                            <video class="shaka-player presize" {!! $ftype !!} controls data-src="{{ $f }}"></video>
                        @else
                            <img class="fdi-imginfo tippyer presize" loading="lazy" data-prewidth="100%" data-preheight="300px" data-content="{{ $i18N->getLanguage(ELanguageText::FileSizeTooLarge) }}" src="{{ asset('assets/images/warning_file_size_large.webp') }}" alt="{{ $virtualFiles->first()->filename }}">
                        @endif
                    @endif
                @endif
                <div class="shaka-playlist"></div>
            </div>
            <div class="pfc-operator">
                <div class="grid-btn-group">
                    @php
                        $random = $shareTable->id; $url = $shareTable->shareURL(); $v = [];
                        if($virtualFiles->isNotEmpty()){
                            $v = $virtualFiles->setVisible(['id','uuid', 'filename', 'size', 'created_at', 'minetypes'])->toArray();
                            foreach ($v as $key => $item) {
                                $v[$key]['size'] = Utils::convertByte($item['size'] ?? 0);
                                $aUrl = route(RouteNameField::APIShareTableItemConversion->value, ['id' => $random, 'fileId' => $item['uuid']]);
                                $dashVideo = \App\Models\DashVideos::where('virtual_file_uuid', '=', $item['uuid'])->get()->first();
                                $btn = "";
                                if($dashVideo !== null){
                                    switch ($dashVideo->type){
                                        case "failed": $btn = '<a data-fn="shareable_conversion_file" data-type="error" data-parent="#conversion_'.$random.'" data-title="是否確認轉換此檔案?" data-id="#conversion_item_'.$key.'" data-confirmboxcontent="此操作將會轉換成檔案" data-href="'.$aUrl.'" class="btn-md btn-border-0 btn btn-ripple btn-error confirm-box"><i class="fa-solid fa-industry"></i>&nbsp;轉換檔案</a>'; break;
                                        case "wait": $btn = "列隊中"; break;
                                        case "success": $btn = "已轉換"; break;
                                    }
                                } else if(Utilsv2::isSupportVideoFile($item["minetypes"])) {
                                    $btn = '<a data-fn="shareable_conversion_file" data-type="error" data-parent="#conversion_'.$random.'" data-title="是否確認轉換此檔案?" data-id="#conversion_item_'.$key.'" data-confirmboxcontent="此操作將會轉換成檔案" data-href="'.$aUrl.'" class="btn-md btn-border-0 btn btn-ripple btn-error confirm-box"><i class="fa-solid fa-industry"></i>&nbsp;轉換檔案</a>';
                                } else { $btn = "不支援轉換檔案"; }
                                $v[$key]['action'] = '<div class="flex gap-3"><div id="conversion_item_'.$key.'" class="autoupdate" '.(($btn !== "") ? 'data-stop="true"': "").' data-fn="get_dash_progress" data-id="'.$item['uuid'].'" ></div>'.$btn.'</div>';
                            }
                        }
                        $isOwner = $shareTable->isOwner($user);
                    @endphp
                    @if($isOwner)
                        <button data-id="{{ $random }}" data-type="conversion" data-href="{{ route(RouteNameField::APIShareTableItemConversion->value, ['id' => $random, 'fileId' => "%fileId%" ]) }}" data-data="{{ json_encode($v) }}" popovertarget="{{ "conversion_".$random }}" class="grid-btn btn-md btn-border-0 btn btn-ripple btn-color3 shareable" data-placement="bottom" title="轉換"><i class="fa-solid fa-file-export"></i></button>
                        @php $relationMember = $shareTable->relationMember(); @endphp
                        <button data-href="{{ $url }}" data-id="{{ $random }}" data-type="share" data-user="{{ route(RouteNameField::APIGetUsers->value) }}" data-users="{{ $relationMember->isNotEmpty() ? $relationMember->pluck('username', 'id')->toTomSelect() : "[]" }}" popovertarget="{{ "shareable_".$random }}" class="grid-btn btn-md btn-border-0 btn btn-ripple btn-color2 shareable" data-placement="bottom" title="分享給"><i class="fa-solid fa-share"></i></button>
                        <button class="grid-btn btn-md btn-border-0 btn btn-ripple btn-ok copyer" data-url="{{ $url }}" data-placement="bottom" title="複製"><i class="fa-solid fa-link"></i></button>
                    @endif
                    @php
                        if($virtualFiles->isNotEmpty()){
                            $v_dl = $virtualFiles->setVisible(['id','uuid', 'filename', 'size', 'created_at'])->toArray();
                            foreach ($v_dl as $key => $item) {
                                $v_dl[$key]['size'] = Utils::convertByte($item['size']);
                                $dashVideo = \App\Models\DashVideos::where('virtual_file_uuid', '=', $item['uuid'])->get()->first();
                                $dashVideoBtn = ($dashVideo && $dashVideo->type === "success") ? '<a target="_blank" rel="noreferrer noopener" href="'.route(RouteNameField::PagePreviewFilePlayerDash->value, ['shareTableId' => $shareTable->id, 'fileId' => $item['uuid'], 'fileName' => $dashVideo->filename.".".$dashVideo->extension]).'" class="btn-md btn-border-0 btn btn-ripple btn-warning"><i class="fa-solid fa-eye"></i>&nbsp;線上串流預覽</a>' : "";
                                $previewBtn = ($item['size'] <= 1024 * 1024 * 400) ? '<a target="_blank" rel="noreferrer noopener" href="%url-0%" class="btn-md btn-border-0 btn btn-ripple btn-warning"><i class="fa-solid fa-eye"></i>&nbsp;完整檔案預覽</a>' : '<a class="btn-md btn-border-0 btn btn-ripple btn-dead"><i class="fa-solid fa-eye"></i>&nbsp;無法完整檔案預覽</a>';
                                if($isOwner){
                                    $v_dl[$key]['action'] = '<div class="flex gap-3">'.$dashVideoBtn.$previewBtn.'<a href="%url-1%" class="btn-md btn-border-0 btn btn-ripple btn-color7 ct" data-fn="download-toast" data-filename="'.e($item["filename"]).'"><i class="fa-solid fa-download"></i>&nbsp;下載</a><a data-fn="shareable_delete_file" data-type="error" data-parent="#download_'.$random.'" data-title="是否確認刪除此檔案?" data-confirmboxcontent="此操作將會永遠的刪除!!" data-href="%url-2%" class="btn-md btn-border-0 btn btn-ripple btn-error confirm-box"><i class="fa-solid fa-trash"></i>&nbsp;刪除</a></div>';
                                } else {
                                    $v_dl[$key]['action'] = '<div class="flex gap-3">'.$dashVideoBtn.$previewBtn.'<a href="%url-1%" class="btn-md btn-border-0 btn btn-ripple btn-color7 ct" data-fn="download-toast" data-filename="'.e($item["filename"]).'"><i class="fa-solid fa-download"></i>&nbsp;下載</a></div>';
                                }
                            }
                        }
                    @endphp
                    <button data-id="{{ $random }}" data-type="download" data-href="{{ route(RouteNameField::PageShareTableItemDownload->value, ['id'=> $shareTable->id, "fileId"=> "%fileId%" ]) }}" data-delete="{{ route(RouteNameField::PageShareTableItemDelete->value, ['id'=> $shareTable->id, "fileId"=> "%fileId%" ]) }}" data-data="{{ json_encode($v_dl ?? []) }}" popovertarget="{{ "download_".$random }}" class="grid-btn btn-md btn-border-0 btn btn-ripple btn-color7 shareable !me-0" data-placement="bottom" title="下載"><i class="fa-solid fa-download"></i></button>
                    @if($isOwner)
                        <button class="grid-btn btn-md btn-border-0 btn btn-ripple btn-warning ct" data-fn="popover4" data-source="{{ $shareTable->id }}" data-target="#{{ $popoverid }}" data-placement="bottom" title="編輯"><i class="fa-solid fa-pen-to-square"></i></button>
                        <button class="grid-btn btn-md btn-border-0 btn btn-ripple btn-error last confirm-box" data-fn="shareable_delete" data-title="你確定要刪除此分享資源?" data-type="error" data-href="{{ route(RouteNameField::PageShareTableDelete->value, ['id'=> $shareTable->id ]) }}" data-placement="bottom" title="刪除"><i class="fa-solid fa-trash"></i></button>
                    @endif
                </div>
            </div>
        </div>
    @endif
@endguest
