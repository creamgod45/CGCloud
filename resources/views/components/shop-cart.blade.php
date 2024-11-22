@php
    use App\View\Components\PopoverOptions;
    $SCpopover = "SC_PW_".\Illuminate\Support\Str::random();
    $fakerprice = 1500;
@endphp
<div class="SC-toggle-btn btn btn-ripple btn-color7 ct" data-fn="toggleable" data-mode="once" data-status="off"
     data-target="#{{ $SCpopover }}" data-lockbody="true" data-whenscrolltopmore="-1"><i
        class="fa-solid fa-cart-shopping"></i></div>
<x-popover-windows class="SC-popover !hidden" data-status="off" popover-title="購物車" :id="$SCpopover"
                   :popover-options="new PopoverOptions()">
    <div class="Shop-Cart">
        <div class="SC-flex-panel">
            <div class="SC-category-list">
                <div class="SC-category-list-inner">
                    <div class="!hidden SC-category-template">
                        <div class="summary" data-category="%category%">%category%</div>
                        <div class="SC-category-items">
                            <div class="SC-sub-category-head">
                                <div class="SC-SCI-remove-btn">移除</div>
                                <div class="SC-SCI-image">圖片</div>
                                <div class="SC-SCI-content">
                                    介紹
                                </div>
                                <div class="SC-SCI-amount">
                                    數量
                                </div>
                                <div class="SC-SCI-price">
                                    價錢
                                </div>
                                <div class="SC-SCI-action">
                                    操作
                                </div>
                            </div>
                            <div class="SC-sub-category-item">
                                <div class="SC-SCI-remove-btn">
                                    <div class="SC-SCI-remove-btn btn btn-ripple btn-color7"><i
                                            class="fa-solid fa-xmark"></i>
                                    </div>
                                </div>
                                <div class="SC-SCI-image placeholder" data-src="%image_url%"></div>
                                <div class="SC-SCI-content">
                                    <div class="title">
                                        %title%
                                    </div>
                                    <div class="description">
                                        %description%
                                    </div>
                                </div>
                                <div class="SC-SCI-amount">
                                    %amount%
                                </div>
                                <div class="SC-SCI-price playwrite-hr-400">
                                    <del>%del_price%</del>
                                    <span>%price%</span>
                                </div>
                                <div class="SC-SCI-action">
                                    <div class="SC-SCI-remove-btn btn btn-ripple btn-color7 btn-md-strip"><i
                                            class="fa-solid fa-plus"></i></div>
                                    <div class="SC-SCI-remove-btn btn btn-ripple btn-color7 btn-md-strip"><i
                                            class="fa-solid fa-minus"></i></div>
                                </div>
                            </div>
                            <div class="SC-sub-category-footer">
                                <div class="SC-SCI-remove-btn">
                                    <div class="SC-SCI-remove-btn btn btn-ripple btn-color7"><i
                                            class="fa-solid fa-xmark"></i>
                                    </div>
                                </div>
                                <div class="SC-SCI-image placeholder"></div>
                                <div class="SC-SCI-content">
                                    總計
                                </div>
                                <div class="SC-SCI-amount">
                                    %total_amount%
                                </div>
                                <div class="SC-SCI-price playwrite-hr-400">
                                    <span>%total_price%</span>
                                </div>
                                <div class="SC-SCI-action">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="!hidden SC-nocategory-template">
                        <div class="SC-category-items">
                            <div class="SC-sub-category-head">
                                <div class="SC-SCI-remove-btn">移除</div>
                                <div class="SC-SCI-image">圖片</div>
                                <div class="SC-SCI-content">
                                    介紹
                                </div>
                                <div class="SC-SCI-amount">
                                    數量
                                </div>
                                <div class="SC-SCI-price">
                                    價錢
                                </div>
                                <div class="SC-SCI-action">
                                    操作
                                </div>
                            </div>
                            <div class="SC-sub-category-item">
                                <div class="SC-SCI-remove-btn">
                                    <div class="SC-SCI-remove-btn btn btn-ripple btn-color7"><i
                                            class="fa-solid fa-xmark"></i>
                                    </div>
                                </div>
                                <div class="SC-SCI-image placeholder" data-src="%image_url%"></div>
                                <div class="SC-SCI-content">
                                    <div class="title">
                                        %title%
                                    </div>
                                    <div class="description">
                                        %description%
                                    </div>
                                </div>
                                <div class="SC-SCI-amount">
                                    %amount%
                                </div>
                                <div class="SC-SCI-price playwrite-hr-400">
                                    <del>%del_price%</del>
                                    <span>%price%</span>
                                </div>
                                <div class="SC-SCI-action">
                                    <div class="SC-SCI-remove-btn btn btn-ripple btn-color7 btn-md-strip"><i
                                            class="fa-solid fa-plus"></i></div>
                                    <div class="SC-SCI-remove-btn btn btn-ripple btn-color7 btn-md-strip"><i
                                            class="fa-solid fa-minus"></i></div>
                                </div>
                            </div>
                            <div class="SC-sub-category-footer">
                                <div class="SC-SCI-remove-btn">
                                    <div class="SC-SCI-remove-btn btn btn-ripple btn-color7"><i
                                            class="fa-solid fa-xmark"></i>
                                    </div>
                                </div>
                                <div class="SC-SCI-image placeholder"></div>
                                <div class="SC-SCI-content">
                                    總計
                                </div>
                                <div class="SC-SCI-amount">
                                    %total_amount%
                                </div>
                                <div class="SC-SCI-price playwrite-hr-400">
                                    <span>%total_price%</span>
                                </div>
                                <div class="SC-SCI-action">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="SC-category-list-title">
                        套餐選擇模式
                        <div id="SC-mode-switcher" data-onclick="true" class="switch">
                            <div class="switch-border">
                                <div class="switch-dot"></div>
                            </div>
                        </div>
                        自由選擇模式
                    </div>
                    <div class="SC-category">
                        <div class="summary" data-category="CPU">CPU</div>
                        <div class="SC-category-items">
                            <div class="SC-sub-category-head">
                                <div class="SC-SCI-remove-btn">移除</div>
                                <div class="SC-SCI-image">圖片</div>
                                <div class="SC-SCI-content">
                                    介紹
                                </div>
                                <div class="SC-SCI-amount">
                                    數量
                                </div>
                                <div class="SC-SCI-price">
                                    價錢
                                </div>
                                <div class="SC-SCI-action">
                                    操作
                                </div>
                            </div>
                            <div class="SC-sub-category-item">
                                <div class="SC-SCI-remove-btn">
                                    <div class="SC-SCI-remove-btn btn btn-ripple btn-color7"><i
                                            class="fa-solid fa-xmark"></i>
                                    </div>
                                </div>
                                <div class="SC-SCI-image placeholder"></div>
                                <div class="SC-SCI-content">
                                    <div class="title">
                                        test
                                    </div>
                                    <div class="description">
                                        test
                                    </div>
                                </div>
                                <div class="SC-SCI-amount">
                                    1
                                </div>
                                <div class="SC-SCI-price playwrite-hr-400">
                                    <del>${{ number_format($fakerprice, 2) }}</del>
                                    <span>${{ number_format($fakerprice/2, 2) }}</span>
                                </div>
                                <div class="SC-SCI-action">
                                    <div class="SC-SCI-remove-btn btn btn-ripple btn-color7 btn-md-strip"><i
                                            class="fa-solid fa-plus"></i></div>
                                    <div class="SC-SCI-remove-btn btn btn-ripple btn-color7 btn-md-strip"><i
                                            class="fa-solid fa-minus"></i></div>
                                </div>
                            </div>
                            <div class="SC-sub-category-item">
                                <div class="SC-SCI-remove-btn">
                                    <div class="SC-SCI-remove-btn btn btn-ripple btn-color7"><i
                                            class="fa-solid fa-xmark"></i>
                                    </div>
                                </div>
                                <div class="SC-SCI-image placeholder"></div>
                                <div class="SC-SCI-content">
                                    <div class="title">
                                        test2
                                    </div>
                                    <div class="description">
                                        test2
                                    </div>
                                </div>
                                <div class="SC-SCI-amount">
                                    1
                                </div>
                                <div class="SC-SCI-price playwrite-hr-400">
                                    <del>${{ number_format($fakerprice, 2) }}</del>
                                    <span>${{ number_format($fakerprice/2, 2) }}</span>
                                </div>
                                <div class="SC-SCI-action">
                                    <div class="SC-SCI-remove-btn btn btn-ripple btn-color7 btn-md-strip"><i
                                            class="fa-solid fa-plus"></i></div>
                                    <div class="SC-SCI-remove-btn btn btn-ripple btn-color7 btn-md-strip"><i
                                            class="fa-solid fa-minus"></i></div>
                                </div>
                            </div>
                            <div class="SC-sub-category-footer">
                                <div class="SC-SCI-remove-btn">
                                    <div class="SC-SCI-remove-btn btn btn-ripple btn-color7"><i
                                            class="fa-solid fa-xmark"></i>
                                    </div>
                                </div>
                                <div class="SC-SCI-image placeholder"></div>
                                <div class="SC-SCI-content">
                                    總計
                                </div>
                                <div class="SC-SCI-amount">
                                    2
                                </div>
                                <div class="SC-SCI-price playwrite-hr-400">
                                    <span>${{ number_format($fakerprice, 2) }}</span>
                                </div>
                                <div class="SC-SCI-action">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="SC-category">
                        <div class="summary" data-category="GPU">GPU</div>
                        <div class="SC-category-items">
                            <div class="SC-sub-category-head">
                                <div class="SC-SCI-remove-btn">移除</div>
                                <div class="SC-SCI-image">圖片</div>
                                <div class="SC-SCI-content">
                                    介紹
                                </div>
                                <div class="SC-SCI-amount">
                                    數量
                                </div>
                                <div class="SC-SCI-price">
                                    價錢
                                </div>
                                <div class="SC-SCI-action">
                                    操作
                                </div>
                            </div>
                            <div class="SC-sub-category-item">
                                <div class="SC-SCI-remove-btn">
                                    <div class="SC-SCI-remove-btn btn btn-ripple btn-color7"><i
                                            class="fa-solid fa-xmark"></i>
                                    </div>
                                </div>
                                <div class="SC-SCI-image placeholder"></div>
                                <div class="SC-SCI-content">
                                    <div class="title">
                                        test
                                    </div>
                                    <div class="description">
                                        test
                                    </div>
                                </div>
                                <div class="SC-SCI-amount">
                                    1
                                </div>
                                <div class="SC-SCI-price playwrite-hr-400">
                                    <del>${{ number_format($fakerprice, 2) }}</del>
                                    <span>${{ number_format($fakerprice/2, 2) }}</span>
                                </div>
                                <div class="SC-SCI-action">
                                    <div class="SC-SCI-remove-btn btn btn-ripple btn-color7 btn-md-strip"><i
                                            class="fa-solid fa-plus"></i></div>
                                    <div class="SC-SCI-remove-btn btn btn-ripple btn-color7 btn-md-strip"><i
                                            class="fa-solid fa-minus"></i></div>
                                </div>
                            </div>
                            <div class="SC-sub-category-item">
                                <div class="SC-SCI-remove-btn">
                                    <div class="SC-SCI-remove-btn btn btn-ripple btn-color7"><i
                                            class="fa-solid fa-xmark"></i>
                                    </div>
                                </div>
                                <div class="SC-SCI-image placeholder"></div>
                                <div class="SC-SCI-content">
                                    <div class="title">
                                        test2
                                    </div>
                                    <div class="description">
                                        test2
                                    </div>
                                </div>
                                <div class="SC-SCI-amount">
                                    1
                                </div>
                                <div class="SC-SCI-price playwrite-hr-400">
                                    <del>${{ number_format($fakerprice, 2) }}</del>
                                    <span>${{ number_format($fakerprice/2, 2) }}</span>
                                </div>
                                <div class="SC-SCI-action">
                                    <div class="SC-SCI-remove-btn btn btn-ripple btn-color7 btn-md-strip"><i
                                            class="fa-solid fa-plus"></i></div>
                                    <div class="SC-SCI-remove-btn btn btn-ripple btn-color7 btn-md-strip"><i
                                            class="fa-solid fa-minus"></i></div>
                                </div>
                            </div>
                            <div class="SC-sub-category-footer">
                                <div class="SC-SCI-remove-btn">
                                    <div class="SC-SCI-remove-btn btn btn-ripple btn-color7"><i
                                            class="fa-solid fa-xmark"></i>
                                    </div>
                                </div>
                                <div class="SC-SCI-image placeholder"></div>
                                <div class="SC-SCI-content">
                                    總計
                                </div>
                                <div class="SC-SCI-amount">
                                    2
                                </div>
                                <div class="SC-SCI-price playwrite-hr-400">
                                    <span>${{ number_format($fakerprice, 2) }}</span>
                                </div>
                                <div class="SC-SCI-action">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{--<div class="SC-category">
                    <div class="summary" data-category="RAM">RAM</div>
                </div>
                <div class="SC-category">
                    <div class="summary" data-category="MB">MB</div>
                </div>
                <div class="SC-category">
                    <div class="summary" data-category="PS">PS</div>
                </div>
                <div class="SC-category">
                    <div class="summary" data-category="Storage">Storage</div>
                </div>
                <div class="SC-category">
                    <div class="summary" data-category="Case">Case</div>
                </div>
                <div class="SC-category">
                    <div class="summary" data-category="Other">Other</div>
                </div>
                <div class="SC-category">
                    <div class="summary" data-category="Notebook">Notebook</div>
                </div>
                <div class="SC-category">
                    <div class="summary" data-category="AllInOne">AllInOne</div>
                </div>
                <div class="SC-category">
                    <div class="summary" data-category="PC">PC</div>
                </div>--}}
            </div>
            <div class="SC-checkout-details">
                <div class="SC-panel">
                    <h1 class="title"><i class="fa-solid fa-money-check-dollar"></i>&nbsp;金額明細</h1>
                    <h1 class="splitter"></h1>
                    <div class="lists">
                        <div>運費：$<span class="SC-freight">0.00</span></div>
                        <div>總金額：$<span class="SC-total-money">0.00</span></div>
                    </div>
                </div>
                <div class="SC-panel">
                    <h1 class="title"><i class="fa-solid fa-bell"></i>&nbsp;通知客服人員</h1>
                    <h1 class="splitter"></h1>
                    <div class="lists2">
                        <div class="SC-print-details btn btn-ripple btn-color7 btn-max btn-center"><i
                                class="fa-solid fa-print"></i>&nbsp;列印明細
                        </div>
                        <div class="SC-send-to-Line btn btn-ripple btn-line btn-max btn-center"><i
                                class="fa-brands fa-line"></i>&nbsp;傳送給客服人員
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="SC-ad-wall">
            <div class="SC-ad-panel">
                <div class="sc-ad-panel-inner">
                    <h1 class="SC-title">你可能感興趣的內容</h1>
                    <div class="SC-ad-card-list">
                        <div class="SC-ad-card placeholder"></div>
                        <div class="SC-ad-card placeholder"></div>
                        <div class="SC-ad-card placeholder"></div>
                        <div class="SC-ad-card placeholder"></div>
                        <div class="SC-ad-card placeholder"></div>
                        <div class="SC-ad-card placeholder"></div>
                        <div class="SC-ad-card placeholder"></div>
                        <div class="SC-ad-card placeholder"></div>
                        <div class="SC-ad-card placeholder"></div>
                        <div class="SC-ad-card placeholder"></div>
                        <div class="SC-ad-card placeholder"></div>
                        <div class="SC-ad-card placeholder"></div>
                        <div class="SC-ad-card placeholder"></div>
                        <div class="SC-ad-card placeholder"></div>
                    </div>
                    <div class="flex justify-end items-center">
                        <div class="btn btn-ripple btn-color7 btn-pill btn-md-strip">瀏覽更多</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-popover-windows>
