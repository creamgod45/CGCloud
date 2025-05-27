@php use App\Lib\Utils\RouteNameField;use App\Lib\Utils\Utils;use App\Lib\Utils\Utilsv2;use App\Lib\I18N\ELanguageText; @endphp
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
         data-placeholderdelay="3000" data-sharetableid="{{ $shareTable->id }}">
        @php
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
                            <div class="pfcf-text">過期日期：{{ $virtualFile->expired_at }}</div>
                            <div class="pfcf-text">
                                擁有者：{{ $virtualFile->members()?->first()?->username }}
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
        <div class="pfc-preview">
            @if($virtualFiles->isNotEmpty())
                @if($virtualFiles->first() !== null)
                    @if($virtualFiles->first() !== null || !empty($virtualFiles))
                        @if(Utilsv2::isSupportImageFile($virtualFiles->first()->minetypes))
                            @php
                                // 獲取圖片物件
                                $image = $virtualFiles->first();
                                $width =  0;
                                $height = 0;
                                if($image !== null){
                                    // 獲取圖片的原始寬度與高度
                                    $image1 = $image->getImage($shareTable->id);
                                    if($image1 !== null){
                                    $width = $image1->getWidth();
                                    $height = $image1->getHeight();
                                    }
                                }

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
                                $poster = "";
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
                                                /* @var \App\Models\VirtualFile $result */
                                                $result = $dashVideos->thumbVirtualFile()->getResults();
                                                $poster = 'data-poster="'.$result->getThumbTemporaryUrl(now()->addMinutes(10)).'"';
                                            }
                                            break;
                                        }
                                    }
                                }
                            @endphp
                            <video class="vjs video-js vjs-theme-forest presize" {{ $ftype }} {!! $poster !!} controls
                                   data-src="{{ $f }}"></video>
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
                                                /* @var \App\Models\VirtualFile $result */
                                                $result = $dashVideos->thumbVirtualFile()->getResults();


                            @endphp
                            <video class="vjs video-js vjs-theme-forest presize"
                                   data-poster="{{$result->getThumbTemporaryUrl(now()->addMinutes(10))}}"
                                   data-type="dash" controls
                                   data-src="{{ route(RouteNameField::APIPreviewFileDash->value, ['shareTableId' => $shareTable->id,'fileId' => $virtualFiles->first()->uuid,'fileName' => $dashVideos->filename.".".$dashVideos->extension]) }}"></video>
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
            @endif
            <div class="vjs-playlist"></div>
        </div>
        <div class="pfc-operator">
            <div class="grid-btn-group">
                @php
                    $random = $shareTable->id;
                    $url = $shareTable->shareURL();
                    $v = [];
                    if($virtualFiles->isNotEmpty()){
                        $v = $virtualFiles->setVisible(['id','uuid', 'filename', 'size', 'created_at', 'minetypes'])->toArray();
                        foreach ($v as $key => $item) {
                            /** @var \App\Models\VirtualFile $item */
                            $v[$key]['size'] = Utils::convertByte($item['size'] ?? 0);
                            $aUrl = route(RouteNameField::APIShareTableItemConversion->value, ['id' => $random, 'fileId' => $item['uuid']]);
                            /** @var \App\Models\DashVideos $dashVideo */
                            $dashVideo = \App\Models\DashVideos::where('virtual_file_uuid', '=', $item['uuid'])->get()->first();
                            if($dashVideo !== null){
                                $btn = "";
                                switch ($dashVideo->type){
                                    case "failed":
                                        $btn = '<a data-fn="shareable_conversion_file" data-type="error" data-parent="#conversion_'.$random.'" data-title="是否確認轉換此檔案?" data-id="#conversion_item_'.$key.'" data-confirmboxcontent="此操作將會轉換成檔案" data-href="'.$aUrl.'" class="btn-md btn-border-0 btn btn-ripple btn-error confirm-box"><i class="fa-solid fa-industry"></i>&nbsp;轉換檔案</a>';
                                        break;
                                    case "wait":
                                        $btn = "列隊中";
                                        break;
                                    case "success":
                                        $btn = "已轉換";
                                        break;
                                }
                            } else if(Utilsv2::isSupportVideoFile($item["minetypes"])) {
                                $btn = '<a data-fn="shareable_conversion_file" data-type="error" data-parent="#conversion_'.$random.'" data-title="是否確認轉換此檔案?" data-id="#conversion_item_'.$key.'" data-confirmboxcontent="此操作將會轉換成檔案" data-href="'.$aUrl.'" class="btn-md btn-border-0 btn btn-ripple btn-error confirm-box"><i class="fa-solid fa-industry"></i>&nbsp;轉換檔案</a>';
                            } else {
                                $btn = "不支援轉換檔案";
                            }
                            $v[$key]['action'] = '<div class="flex gap-3"><div id="conversion_item_'.$key.'" class="autoupdate" '.(($btn !== "") ? 'data-stop="true"': "").' data-fn="get_dash_progress" data-id="'.$item['uuid'].'" ></div>'.$btn.'</div>';
                        }
                    }
                @endphp
                @if($shareTable->isOwner(\Illuminate\Support\Facades\Auth::user()))
                    <a data-id="{{ $random }}"
                       data-type="conversion"
                       data-href="{{ route(RouteNameField::APIShareTableItemConversion->value, ['id' => $random, 'fileId' => "%fileId%" ]) }}"
                       data-data="{{ json_encode($v) }}"
                       popovertarget="{{ "conversion_".$random }}"
                       class="grid-btn btn-md btn-border-0 btn btn-ripple btn-color3 shareable"
                       data-placement="bottom"
                       title="轉換"><i class="fa-solid fa-file-export"></i></a>
                    @php
                        $relationMember = $shareTable->relationMember();
                    @endphp
                    <a data-href="{{ $url }}"
                       data-id="{{ $random }}"
                       data-type="share"
                       data-user="{{ route(RouteNameField::APIGetUsers->value) }}"
                       data-users="{{ $relationMember->isNotEmpty() ? $relationMember->pluck('username', 'id')->toTomSelect() : "[]" }}"
                       popovertarget="{{ "shareable_".$random }}"
                       class="grid-btn btn-md btn-border-0 btn btn-ripple btn-color2 shareable"
                       data-placement="bottom"
                       title="分享給"><i class="fa-solid fa-share"></i></a>
                @endif
                @if($shareTable->isOwner(\Illuminate\Support\Facades\Auth::user()))
                    <div class="grid-btn btn-md btn-border-0 btn btn-ripple btn-ok copyer"
                         data-url="{{ $url }}"
                         data-placement="bottom"
                         title="複製">
                        <i class="fa-solid fa-link"></i>
                    </div>
                @endif
                @php
                    $random1 = $shareTable->id;
                    $url = $shareTable->shareURL();
                    $v = [];
                    if($virtualFiles->isNotEmpty()){
                        $v = $virtualFiles->setVisible(['id','uuid', 'filename', 'size', 'created_at'])->toArray();
                        foreach ($v as $key => $item) {
                            $v[$key]['size'] = Utils::convertByte($item['size']);
                            $dashVideo = \App\Models\DashVideos::where('virtual_file_uuid', '=', $item['uuid'])->get()->first();
                            $dashVideoBtn = "";
                            if($dashVideo !== null){
                                if($dashVideo->type === "success"){
                                    $url = route(RouteNameField::PagePreviewFilePlayerDash->value, [
                                        'shareTableId' => $shareTable->id,
                                        'fileId' => $item['uuid'],
                                        'fileName' => $dashVideo->filename.".".$dashVideo->extension,
                                    ]);
                                    $dashVideoBtn = '<a target="_blank" rel="noreferrer noopener" json="'.$dashVideo->toJson().'" href="'.$url.'" class="btn-md btn-border-0 btn btn-ripple btn-warning"><i class="fa-solid fa-eye"></i>&nbsp;線上串流預覽</a>';
                                }
                            }
                            $previewBtn = "";
                            if($item['size'] <= 1024 * 1024 * 400){
                                $previewBtn = '<a target="_blank" rel="noreferrer noopener" href="%url-0%" class="btn-md btn-border-0 btn btn-ripple btn-warning"><i class="fa-solid fa-eye"></i>&nbsp;完整檔案預覽</a>';
                            } else {
                                $previewBtn = '<a target="_blank" rel="noreferrer noopener" class="btn-md btn-border-0 btn btn-ripple btn-dead"><i class="fa-solid fa-eye"></i>&nbsp;無法完整檔案預覽</a>';
                            }
                            if($shareTable->member_id === \Illuminate\Support\Facades\Auth::user()?->id){
                                $v[$key]['action'] = '<div class="flex gap-3">'.$dashVideoBtn.$previewBtn.'<a href="%url-1%" class="btn-md btn-border-0 btn btn-ripple btn-color7 ct" data-fn="download-toast" data-filename="'.e($item["filename"]).'"><i class="fa-solid fa-download"></i>&nbsp;下載</a><a data-fn="shareable_delete_file" data-type="error" data-parent="#download_'.$random1.'" data-title="是否確認刪除此檔案?" data-confirmboxcontent="此操作將會永遠的刪除!!" data-href="%url-2%" class="btn-md btn-border-0 btn btn-ripple btn-error confirm-box"><i class="fa-solid fa-trash"></i>&nbsp;刪除</a></div>';
                            } else {
                                $v[$key]['action'] = '<div class="flex gap-3">'.$dashVideoBtn.$previewBtn.'<a href="%url-1%" class="btn-md btn-border-0 btn btn-ripple btn-color7 ct" data-fn="download-toast" data-filename="'.e($item["filename"]).'"><i class="fa-solid fa-download"></i>&nbsp;下載</a></div>';
                            }
                        }
                    }
                @endphp
                <div data-id="{{ $random1 }}"
                     data-type="download"
                     data-href="{{ route(RouteNameField::PageShareTableItemDownload->value, ['id'=> $shareTable->id,"fileId"=> "%fileId%" ]) }}"
                     data-delete="{{ route(RouteNameField::PageShareTableItemDelete->value, ['id'=> $shareTable->id,"fileId"=> "%fileId%" ]) }}"
                     data-data="{{ json_encode($v) }}"
                     popovertarget="{{ "download_".$random1 }}"
                     class="grid-btn btn-md btn-border-0 btn btn-ripple btn-color7 shareable !me-0"
                     data-placement="bottom"
                     title="下載"><i class="fa-solid fa-download"></i></div>
                @if($shareTable->isOwner(\Illuminate\Support\Facades\Auth::user()))
                    <div class="grid-btn btn-md btn-border-0 btn btn-ripple btn-warning ct"
                         data-fn="popover4" data-source="{{ $shareTable->id }}"
                         data-target="#{{ $popoverid }}"
                         data-placement="bottom"
                         title="編輯"><i class="fa-solid fa-pen-to-square"></i></div>
                    <div class="grid-btn btn-md btn-border-0 btn btn-ripple btn-error last confirm-box"
                         data-fn="shareable_delete"
                         data-title="你確定要刪除此分享資源?"
                         data-confirmboxcontent="此操作將會永遠的刪除!!"
                         data-type="error"
                         data-href="{{ route(RouteNameField::PageShareTableDelete->value, ['id'=> $shareTable->id ]) }}"
                         data-placement="bottom"
                         title="刪除"><i class="fa-solid fa-trash"></i>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endauth
