@use(App\Lib\I18N\ELanguageText;use App\Lib\Utils\Utilsv2)
@if ($elements->hasPages())
    @if($nopaginationframe !== 1)
        <div class="pagination-frame">
            @endif
            {{-- Pagination Elements --}}
            @if($headerPageAction)
                <div class="pagination">
                    <div class="btn-group btn-group-border-2-slate">
                        <a class="btn btn-center pg-info pg-btn btn-md btn-dead tracking-widest !break-keep !bg-color1 btn-border-0">{{ $elements->currentPage() }}
                            /{{ $elements->lastPage() }}</a>
                        @if ($elements->onFirstPage())
                            <a aria-label="{{$i18N->getLanguage(ELanguageText::pagination_previous)}}"
                               class="btn btn-center pg-btn btn-dead btn-md btn-border-0"><i class="fa-solid fa-left-long"></i></a>
                        @else
                            <a href="{{ Utilsv2::mergeUrl(url()->full(), $elements->previousPageUrl()) }}"
                               rel="prev"
                               aria-label="{{$i18N->getLanguage(ELanguageText::pagination_previous)}}"
                               class="btn btn-center pg-btn btn-ripple btn-md btn-color1 btn-border-0"><i
                                    class="fa-solid fa-left-long"></i></a>
                        @endif
                        <div class="scroll-list">
                            @if($elements->lastPage() >= 11)
                                <a href="{{ $elements->url(1) }}" class="!rounded-none btn btn-center pg-btn @if($elements->currentPage() === 1) btn-dead pagination-btn-active @else btn-ripple btn-color1 @endif btn-md btn-border-0">1</a>
                                <a href="{{ $elements->url(2) }}" class="btn btn-center pg-btn @if($elements->currentPage() === 2) btn-dead pagination-btn-active @else btn-ripple btn-color1 @endif btn-md btn-border-0">2</a>
                                <a href="{{ $elements->url(3) }}" class="btn btn-center pg-btn @if($elements->currentPage() === 3) btn-dead pagination-btn-active @else btn-ripple btn-color1 @endif btn-md btn-border-0">3</a>
                                <a href="{{ $elements->url(4) }}" class="btn btn-center pg-btn @if($elements->currentPage() === 4) btn-dead pagination-btn-active @else btn-ripple btn-color1 @endif btn-md btn-border-0">4</a>
                                <a href="{{ $elements->url(5) }}" class="btn btn-center pg-btn @if($elements->currentPage() === 5) btn-dead pagination-btn-active @else btn-ripple btn-color1 @endif btn-md btn-border-0">5</a>
                                @if($elements->currentPage() === 6)
                                    <a href="{{ $elements->url($elements->currentPage()) }}" class="btn btn-center pg-btn btn-dead btn-md btn-border-0 pagination-btn-active">{{ $elements->currentPage() }}</a>
                                    <a href="{{ $elements->url($elements->currentPage()+1) }}" class="btn btn-center pg-btn btn-ripple btn-color1 btn-md btn-border-0">{{ $elements->currentPage()+1 }}</a>
                                    <a href="{{ $elements->url($elements->currentPage()+2) }}" class="btn btn-center pg-btn btn-ripple btn-color1 btn-md btn-border-0">{{ $elements->currentPage()+2 }}</a>
                                    <a class="btn btn-center pg-btn btn-md btn-dead btn-border-0">...</a>
                                @elseif($elements->currentPage() === ($elements->lastPage()-5))
                                    <a class="btn btn-center pg-btn btn-md btn-dead btn-border-0">...</a>
                                    <a href="{{ $elements->url($elements->currentPage()-2) }}" class="btn btn-center pg-btn btn-ripple btn-color1 btn-md btn-border-0">{{ $elements->currentPage()-2 }}</a>
                                    <a href="{{ $elements->url($elements->currentPage()-1) }}" class="btn btn-center pg-btn btn-ripple btn-color1 btn-md btn-border-0">{{ $elements->currentPage()-1 }}</a>
                                    <a href="{{ $elements->url($elements->currentPage()) }}" class="btn btn-center pg-btn btn-dead btn-md btn-border-0 pagination-btn-active">{{ $elements->currentPage() }}</a>
                                @elseif($elements->currentPage() > 5 && $elements->currentPage() < ($elements->lastPage()-4))
                                    <a class="btn btn-center pg-btn btn-md btn-dead btn-border-0">...</a>
                                    <a href="{{ $elements->url($elements->currentPage()-1) }}" class="btn btn-center pg-btn btn-ripple btn-color1 btn-md btn-border-0">{{ $elements->currentPage()-1 }}</a>
                                    <a href="{{ $elements->url($elements->currentPage()) }}" class="btn btn-center pg-btn btn-dead btn-md btn-border-0 pagination-btn-active">{{ $elements->currentPage() }}</a>
                                    <a href="{{ $elements->url($elements->currentPage()+1) }}" class="btn btn-center pg-btn btn-ripple btn-color1 btn-md btn-border-0">{{ $elements->currentPage()+1 }}</a>
                                    <a class="btn btn-center pg-btn btn-md btn-dead btn-border-0">...</a>
                                @else
                                    <a class="btn btn-center pg-btn btn-md btn-dead btn-border-0">...</a>
                                @endif
                                <a href="{{ $elements->url($elements->lastPage()-4) }}" class="btn btn-center pg-btn btn-ripple @if($elements->currentPage() === $elements->lastPage()-4) btn-dead pagination-btn-active @else btn-ripple btn-color1 @endif btn-border-0">{{ $elements->lastPage()-4 }}</a>
                                <a href="{{ $elements->url($elements->lastPage()-3) }}" class="btn btn-center pg-btn btn-ripple @if($elements->currentPage() === $elements->lastPage()-3) btn-dead pagination-btn-active @else btn-ripple btn-color1 @endif btn-border-0">{{ $elements->lastPage()-3 }}</a>
                                <a href="{{ $elements->url($elements->lastPage()-2) }}" class="btn btn-center pg-btn btn-ripple @if($elements->currentPage() === $elements->lastPage()-2) btn-dead pagination-btn-active @else btn-ripple btn-color1 @endif btn-border-0">{{ $elements->lastPage()-2}}</a>
                                <a href="{{ $elements->url($elements->lastPage()-1) }}" class="btn btn-center pg-btn btn-ripple @if($elements->currentPage() === $elements->lastPage()-1) btn-dead pagination-btn-active @else btn-ripple btn-color1 @endif btn-border-0">{{ $elements->lastPage()-1 }}</a>
                                <a href="{{ $elements->url($elements->lastPage()) }}" class="!rounded-none btn btn-center pg-btn @if($elements->currentPage() === $elements->lastPage()) btn-dead pagination-btn-active @else btn-ripple btn-color1 @endif btn-md btn-border-0">{{ $elements->lastPage() }}</a>
                            @else
                                {{-- 當總頁數小於 11 頁時，顯示所有頁碼 --}}
                                @for($i = 1, $j=0; $i <= $elements->lastPage(); $i++)
                                    @if($elements->currentPage() === $i)
                                        <a class="btn btn-center pg-btn btn-md pagination-btn-active btn-dead btn-border-0">{{ $i }}</a>
                                    @else
                                        <a href="{{ $elements->url($i) }}"
                                           class="btn btn-center pg-btn btn-ripple btn-md btn-color1 btn-border-0">{{ $i }}</a>
                                    @endif
                                @endfor
                            @endif
                        </div>
                        @if ($elements->hasMorePages())
                            <a href="{{ Utilsv2::mergeUrl(url()->full(), $elements->nextPageUrl()) }}"
                               rel="next"
                               aria-label="{{$i18N->getLanguage(ELanguageText::pagination_next)}}"
                               class="btn btn-center pg-btn btn-ripple btn-md btn-color1 btn-border-0"><i
                                    class="fa-solid fa-right-long"></i></a>
                        @else
                            <a aria-label="{{$i18N->getLanguage(ELanguageText::pagination_next)}}"
                               aria-hidden="true"
                               class="btn btn-center pg-btn btn-dead btn-md btn-border-0"><i
                                    class="fa-solid fa-right-long"></i></a>
                        @endif
                    </div>
                </div>
            @endif
            {{$slot}}
            <div class="pagination">
                <div class="btn-group btn-group-border-2-slate">
                    <a class="btn btn-center pg-btn btn-md btn-dead tracking-widest !break-keep !bg-color1 btn-border-0">{{ $elements->currentPage() }}
                        /{{ $elements->lastPage() }}</a>
                    @if ($elements->onFirstPage())
                        <a aria-label="{{$i18N->getLanguage(ELanguageText::pagination_previous)}}"
                           class="btn btn-center pg-btn btn-dead btn-md btn-border-0"><i class="fa-solid fa-left-long"></i></a>
                    @else
                        <a href="{{ Utilsv2::mergeUrl(url()->full(), $elements->previousPageUrl()) }}"
                           rel="prev"
                           aria-label="{{$i18N->getLanguage(ELanguageText::pagination_previous)}}"
                           class="btn btn-center pg-btn btn-ripple btn-md btn-color1 btn-border-0"><i
                                class="fa-solid fa-left-long"></i></a>
                    @endif
                    <div class="scroll-list">

                        @if($elements->lastPage() >= 11)
                            <a href="{{ $elements->url(1) }}" class="!rounded-none btn btn-center pg-btn @if($elements->currentPage() === 1) btn-dead @else btn-ripple btn-color1 @endif btn-md btn-border-0">1</a>
                            <a href="{{ $elements->url(2) }}" class="btn btn-center pg-btn @if($elements->currentPage() === 2) btn-dead @else btn-ripple btn-color1 @endif btn-md btn-border-0">2</a>
                            <a href="{{ $elements->url(3) }}" class="btn btn-center pg-btn @if($elements->currentPage() === 3) btn-dead @else btn-ripple btn-color1 @endif btn-md btn-border-0">3</a>
                            <a href="{{ $elements->url(4) }}" class="btn btn-center pg-btn @if($elements->currentPage() === 4) btn-dead @else btn-ripple btn-color1 @endif btn-md btn-border-0">4</a>
                            <a href="{{ $elements->url(5) }}" class="btn btn-center pg-btn @if($elements->currentPage() === 5) btn-dead @else btn-ripple btn-color1 @endif btn-md btn-border-0">5</a>
                            @if($elements->currentPage() === 6)
                                <a href="{{ $elements->url($elements->currentPage()) }}" class="btn btn-center pg-btn btn-dead btn-md btn-border-0">{{ $elements->currentPage() }}</a>
                                <a href="{{ $elements->url($elements->currentPage()+1) }}" class="btn btn-center pg-btn btn-ripple btn-color1 btn-md btn-border-0">{{ $elements->currentPage()+1 }}</a>
                                <a href="{{ $elements->url($elements->currentPage()+2) }}" class="btn btn-center pg-btn btn-ripple btn-color1 btn-md btn-border-0">{{ $elements->currentPage()+2 }}</a>
                                <a class="btn btn-center pg-btn btn-md btn-dead btn-border-0">...</a>
                            @elseif($elements->currentPage() === ($elements->lastPage()-5))
                                <a class="btn btn-center pg-btn btn-md btn-dead btn-border-0">...</a>
                                <a href="{{ $elements->url($elements->currentPage()-2) }}" class="btn btn-center pg-btn btn-ripple btn-color1 btn-md btn-border-0">{{ $elements->currentPage()-2 }}</a>
                                <a href="{{ $elements->url($elements->currentPage()-1) }}" class="btn btn-center pg-btn btn-ripple btn-color1 btn-md btn-border-0">{{ $elements->currentPage()-1 }}</a>
                                <a href="{{ $elements->url($elements->currentPage()) }}" class="btn btn-center pg-btn btn-dead btn-md btn-border-0">{{ $elements->currentPage() }}</a>
                            @elseif($elements->currentPage() > 5 && $elements->currentPage() < ($elements->lastPage()-4))
                                <a class="btn btn-center pg-btn btn-md btn-dead btn-border-0">...</a>
                                <a href="{{ $elements->url($elements->currentPage()-1) }}" class="btn btn-center pg-btn btn-ripple btn-color1 btn-md btn-border-0">{{ $elements->currentPage()-1 }}</a>
                                <a href="{{ $elements->url($elements->currentPage()) }}" class="btn btn-center pg-btn btn-dead btn-md btn-border-0">{{ $elements->currentPage() }}</a>
                                <a href="{{ $elements->url($elements->currentPage()+1) }}" class="btn btn-center pg-btn btn-ripple btn-color1 btn-md btn-border-0">{{ $elements->currentPage()+1 }}</a>
                                <a class="btn btn-center pg-btn btn-md btn-dead btn-border-0">...</a>
                            @else
                                <a class="btn btn-center pg-btn btn-md btn-dead btn-border-0">...</a>
                            @endif
                            <a href="{{ $elements->url($elements->lastPage()-4) }}" class="btn btn-center pg-btn btn-ripple @if($elements->currentPage() === $elements->lastPage()-4) btn-dead @else btn-ripple btn-color1 @endif btn-border-0">{{ $elements->lastPage()-4 }}</a>
                            <a href="{{ $elements->url($elements->lastPage()-3) }}" class="btn btn-center pg-btn btn-ripple @if($elements->currentPage() === $elements->lastPage()-3) btn-dead @else btn-ripple btn-color1 @endif btn-border-0">{{ $elements->lastPage()-3 }}</a>
                            <a href="{{ $elements->url($elements->lastPage()-2) }}" class="btn btn-center pg-btn btn-ripple @if($elements->currentPage() === $elements->lastPage()-2) btn-dead @else btn-ripple btn-color1 @endif btn-border-0">{{ $elements->lastPage()-2}}</a>
                            <a href="{{ $elements->url($elements->lastPage()-1) }}" class="btn btn-center pg-btn btn-ripple @if($elements->currentPage() === $elements->lastPage()-1) btn-dead @else btn-ripple btn-color1 @endif btn-border-0">{{ $elements->lastPage()-1 }}</a>
                            <a href="{{ $elements->url($elements->lastPage()) }}" class="!rounded-none btn btn-center pg-btn @if($elements->currentPage() === $elements->lastPage()) btn-dead @else btn-ripple btn-color1 @endif btn-md btn-border-0">{{ $elements->lastPage() }}</a>
                        @else
                            {{-- 當總頁數小於 11 頁時，顯示所有頁碼 --}}
                            @for($i = 1, $j=0; $i <= $elements->lastPage(); $i++)
                                @if($elements->currentPage() === $i)
                                    <a class="btn btn-center pg-btn btn-md btn-dead btn-border-0">{{ $i }}</a>
                                @else
                                    <a href="{{ $elements->url($i) }}"
                                       class="btn btn-center pg-btn btn-ripple btn-md btn-color1 btn-border-0">{{ $i }}</a>
                                @endif
                            @endfor
                        @endif
                    </div>
                    @if ($elements->hasMorePages())
                        <a href="{{ Utilsv2::mergeUrl(url()->full(), $elements->nextPageUrl()) }}"
                           rel="next"
                           aria-label="{{$i18N->getLanguage(ELanguageText::pagination_next)}}"
                           class="btn btn-center pg-btn btn-ripple btn-md btn-color1 btn-border-0"><i
                                class="fa-solid fa-right-long"></i></a>
                    @else
                        <a aria-label="{{$i18N->getLanguage(ELanguageText::pagination_next)}}"
                           aria-hidden="true"
                           class="btn btn-center pg-btn btn-dead btn-md btn-border-0"><i
                                class="fa-solid fa-right-long"></i></a>
                    @endif
                </div>
            </div>
            @if($nopaginationframe !== 1)
        </div>
    @endif
@else
    @if($nopaginationframe !== 1)
        <div class="pagination-frame">
            @endif
            {{$slot}}
            @if($nopaginationframe !== 1)
        </div>
    @endif
@endif
