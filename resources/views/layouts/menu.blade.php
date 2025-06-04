@php
    /***
     * @var string[] $urlParams 當前 URL 參數
     * @var array $moreParams 更多參數
     * @var I18N $i18N I18N 本地化語言系統
     * @var \Illuminate\Http\Request $request 請求
     * @var string $fingerprint 客戶端指紋
     * @var string $theme 主題

     * @var \App\Models\ShopConfig[] $styleConfig 系統設定
     */
@endphp
@use(App\Lib\I18N\ELanguageText;use App\Lib\I18N\I18N;use App\Lib\Type\Array\CGArray;use App\Lib\Utils\Utils;use App\Lib\Utils\RouteNameField)
<nav class="app-header">
    <div class="Thin-Panel">
        <a href="{{ route(RouteNameField::PageHome->value) }}" class="logo placeholder placeholder-circle lazyImg"
           data-placeholderdelay="100"
           data-src="{{asset("assets/images/logo.ico")}}"></a>
        <a href="{{ route(RouteNameField::PageHome->value) }}" class="title">
            CGCloud
        </a>
        <div class="flex items-center justify-end">
            <div id="dropdown1" data-theme="light-border" data-placement="bottom" data-content="#dropdown-status1" data-htmlable="true" class="tippyer menu btn btn-md-strip btn-ripple btn-color7">
                <div class="menu-title noto-serif-tc-black">選單</div>
                <span><i class="fa-solid fa-caret-down"></i></span>
            </div>
        </div>
    </div>
</nav>
<div id="dropdown-status1" class="dropdown-menu !hidden">
    <a href="{{ route(RouteNameField::PageHome) }}" class="dropdown-menu-item btn btn-color7 btn-md-strip btn-ripple btn-border-0" onmouseup="document.dispatchEvent(new CustomEvent('CG::BtnRipple', { detail: this}))">
        <div class="icon"><i class="fa-solid fa-computer"></i></div>
        <div class="dropdown-menu-title noto-serif-tc-black">首頁</div>
    </a>
    <a href="{{ route(RouteNameField::PageHome) }}" class="dropdown-menu-item btn btn-color7 btn-md-strip btn-ripple btn-border-0" onmouseup="document.dispatchEvent(new CustomEvent('CG::BtnRipple', { detail: this}))">
        <div class="icon"><i class="fa-solid fa-upload"></i></div>
        <div class="dropdown-menu-title noto-serif-tc-black">上傳檔案</div>
    </a>
    <a href="{{ route(RouteNameField::PageMyShareTables) }}" class="dropdown-menu-item btn btn-color7 btn-md-strip btn-ripple btn-border-0" onmouseup="document.dispatchEvent(new CustomEvent('CG::BtnRipple', { detail: this}))">
        <div class="icon"><i class="fa-solid fa-warehouse"></i></div>
        <div class="dropdown-menu-title noto-serif-tc-black">管理檔案</div>
    </a>
    <div class="dropdown-menu-item btn btn-color7 btn-md-strip btn-ripple btn-border-0" onmouseup="document.dispatchEvent(new CustomEvent('CG::BtnRipple', { detail: this}))">
        <div class="icon"><i class="fa-solid fa-address-card"></i></div>
        <div class="dropdown-menu-title noto-serif-tc-black">個人設定</div>
    </div>
    <a class="dropdown-menu-item btn btn-md-strip btn-color7 btn-ripple btn-border-0 ct dark-mode-trigger" onmouseup="document.dispatchEvent(new CustomEvent('CG::Dark', { detail: this}))">
        <div class="icon"><i class="fa-solid fa-gears"></i></div>
        <div class="dropdown-menu-title noto-serif-tc-black">黑暗模式</div>
    </a>
    <div class="dropdown-menu-separator"></div>
    @guest
    <a href="{{ route(RouteNameField::PageLogin->value) }}" class="dropdown-menu-item btn btn-color7 btn-md-strip btn-ripple btn-border-0" onmouseup="document.dispatchEvent(new CustomEvent('CG::BtnRipple', { detail: this}))">
        <div class="icon"><i class="fa-solid fa-computer"></i></div>
        <div class="dropdown-menu-title noto-serif-tc-black">登入</div>
    </a>
    <a href="{{ route(RouteNameField::PageRegister->value) }}" class="dropdown-menu-item btn btn-color7 btn-md-strip btn-ripple btn-border-0" onmouseup="document.dispatchEvent(new CustomEvent('CG::BtnRipple', { detail: this}))">
        <div class="icon"><i class="fa-solid fa-computer"></i></div>
        <div class="dropdown-menu-title noto-serif-tc-black">註冊</div>
    </a>
    @endguest
    @auth
    <a class="dropdown-menu-item btn btn-dead btn-md-strip btn-border-0 text-black">
        <div class="icon"><i class="fa-solid fa-user"></i></div>
        <div class="dropdown-menu-title noto-serif-tc-black">{{ (\Illuminate\Support\Facades\Auth::user() !== null) ? \Illuminate\Support\Facades\Auth::user()->username : ""  }}</div>
    </a>
    <a href="{{ route(RouteNameField::PageLogout->value) }}" class="dropdown-menu-item btn btn-color7 btn-md-strip btn-ripple btn-border-0" onmouseup="document.dispatchEvent(new CustomEvent('CG::BtnRipple', { detail: this}))">
        <div class="icon"><i class="fa-solid fa-computer"></i></div>
        <div class="dropdown-menu-title noto-serif-tc-black">登出</div>
    </a>
    @endauth
</div>
{{-- <menu class="float-menu" data-sticky-container>
    @if(\Illuminate\Support\Facades\Route::current()->getName() === RouteNameField::PageHome->value ||
        \Illuminate\Support\Facades\Route::current()->getName() === RouteNameField::PageHome2->value ||
        \Illuminate\Support\Facades\Route::current()->getName() === RouteNameField::PageShopItem->value ||
        \Illuminate\Support\Facades\Route::current()->getName() === RouteNameField::PageSearchShopItem->value)
        <div class="menu sidemenu-btn btn btn-md-strip btn-color3 btn-ripple btn-border-0 ct"
             data-fn="toggleable" data-whenscreenless="-1" data-whenscrolltopmore="-1" data-mode="once"
             data-target="#filter-bar" data-animation="true" data-animationstat1="closeing"
             data-animationstat1-1="filter-bar-on" data-animationstat2="opening"
             data-animationstat2-1="filter-bar-off" data-animationduration="2000">
            <div class="dropdown-menu-title noto-serif-tc-black"><i class="fa-solid fa-filter"></i> 篩選</div>
        </div>
    @endif
    <a class="menu template btn btn-md-strip btn-ok btn-ripple btn-border-0">
        <div class="icon"><i class="!hidden off fa-solid fa-square-minus"></i><i class="on fa-solid fa-square-plus"></i></div>
        <div class="dropdown-menu-title noto-serif-tc-black">展開選單</div>
    </a>
    <a href="{{ route(RouteNameField::PageHome->value) }}" data-hrefs="{{ route(RouteNameField::PageSearchShopItem->value) }},{{ route(RouteNameField::PageShopItem->value, ['id' => '#shopitemid']) }}"
       class="menu btn btn-md-strip btn-color7 btn-ripple btn-border-0 !hidden">
        <div class="icon"><i class="fa-solid fa-house"></i></div>
        <div class="dropdown-menu-title noto-serif-tc-black">首頁</div>
    </a>
    <div class="menu btn btn-md-strip btn-color7 btn-ripple btn-border-0 !hidden">
        <div class="icon"><i class="fa-solid fa-phone-volume"></i></div>
        <div class="dropdown-menu-title noto-serif-tc-black">聯絡我們</div>
    </div>
    <div id="dropdown1" data-theme="light-border" data-placement="bottom" data-content="#dropdown-status1" data-htmlable="true" class="tippyer menu btn btn-md-strip btn-ripple btn-color7 btn-border-0 !hidden">
        <div class="icon"><i class="fa-solid fa-money-bill-1"></i></div>
        <div class="dropdown-menu-title noto-serif-tc-black">線上估價</div>
        <span><i class="fa-solid fa-caret-down"></i></span>
    </div>
    <div class="menu btn btn-md-strip btn-color7 btn-ripple btn-border-0 !hidden">
        <div class="icon"><i class="fa-brands fa-golang"></i></div>
        <div class="dropdown-menu-title noto-serif-tc-black">到府服務</div>
    </div>
    <div class="menu btn btn-md-strip btn-color7 btn-ripple btn-border-0 !hidden">
        <div class="icon"><i class="fa-solid fa-location-dot"></i></div>
        <div class="dropdown-menu-title noto-serif-tc-black">全台據點</div>
    </div>
    <div class="menu btn btn-md-strip btn-color7 btn-ripple btn-border-0 !hidden">
        <div class="icon"><i class="fa-solid fa-route"></i></div>
        <div class="dropdown-menu-title noto-serif-tc-black">購買流程</div>
    </div>
    <a href="{{ route(RouteNameField::PageAddShopItem->value) }}"
       class="menu btn btn-md-strip btn-color7 btn-ripple btn-border-0 !hidden">
        <div class="icon"><i class="fa-solid fa-plus"></i></div>
        <div class="dropdown-menu-title noto-serif-tc-black">新增產品</div>
    </a>
    <a href="{{ route(RouteNameField::PageShopItemList->value) }}"
       class="menu btn btn-md-strip btn-color7 btn-ripple btn-border-0 !hidden">
        <div class="icon"><i class="fa-solid fa-warehouse"></i></div>
        <div class="dropdown-menu-title noto-serif-tc-black">庫存管理</div>
    </a>
    <a href="{{ route(RouteNameField::PageSystemSettings->value) }}"
       class="menu btn btn-md-strip btn-color7 btn-ripple btn-border-0 !hidden">
        <div class="icon"><i class="fa-solid fa-gears"></i></div>
        <div class="dropdown-menu-title noto-serif-tc-black">系統管理</div>
    </a>
    <a href="{{ route(RouteNameField::PageCustomPages->value) }}"
       class="menu btn btn-md-strip btn-color7 btn-ripple btn-border-0 !hidden">
        <div class="icon"><i class="fa-solid fa-folder"></i></div>
        <div class="dropdown-menu-title noto-serif-tc-black">所有自訂頁面</div>
    </a>
    <a class="menu btn btn-md-strip btn-color7 btn-ripple btn-border-0 ct dark-mode-trigger !hidden">
        <div class="icon"><i class="fa-solid fa-gears"></i></div>
        <div class="dropdown-menu-title noto-serif-tc-black">黑暗模式</div>
    </a>
    @guest
        <div id="dropdown3" data-theme="light-border" data-placement="bottom" data-content="#dropdown-status3" data-htmlable="true" class="tippyer menu btn btn-md-strip btn-ripple btn-color7 btn-border-0 !hidden" data-menuable="true" data-hrefs="{{ route(RouteNameField::PageLogin->value) }},{{ route(RouteNameField::PageRegister->value) }}">
            <div class="icon"><i class="fa-solid fa-money-bill-1"></i></div>
            <div class="dropdown-menu-title noto-serif-tc-black">會員操作</div>
            <span><i class="fa-solid fa-caret-down"></i></span>
        </div>
    @endguest
    @auth
        @php
            $user = Auth::user();
        @endphp
        @if(!$user->hasVerifiedEmail())
            <a href="{{route(RouteNameField::PageEmailReSendMailVerification->value)}}" data-hrefs="{{ route(RouteNameField::PageEmailReSendMailVerification->value) }}" class="menu-btn btn btn-color7 btn-md-strip btn-ripple btn-border-0 !hidden">
                <i class="fa-solid fa-paper-plane"></i>
                <span>&nbsp;重新驗證</span>
            </a>
        @endif
        <div id="dropdown2" data-theme="light-border" data-placement="bottom" data-content="#dropdown-status2" data-htmlable="true" class="tippyer menu btn btn-md-strip btn-ripple btn-color7 btn-border-0 !hidden" data-menuable="true" data-hrefs="{{ route(RouteNameField::PageProfile->value) }},{{ route(RouteNameField::PageMembers->value) }},{{ route(RouteNameField::PageLogout->value) }}">
            <div class="icon"><i class="fa-solid fa-money-bill-1"></i></div>
            <div class="dropdown-menu-title noto-serif-tc-black">會員操作</div>
            <span><i class="fa-solid fa-caret-down"></i></span>
        </div>
    @endauth
</menu>
@guest
<div id="dropdown-status3" class="dropdown-menu !hidden">
    <a href="{{ route(RouteNameField::PageLogin->value) }}" class="dropdown-menu-item btn btn-color7 btn-md-strip btn-ripple btn-border-0" onmouseup="document.dispatchEvent(new CustomEvent('CG::BtnRipple', { detail: this}))">
        <div class="icon"><i class="fa-solid fa-right-to-bracket"></i></div>
        <div class="dropdown-menu-title noto-serif-tc-black">登入</div>
    </a>
    <a href="{{ route(RouteNameField::PageRegister->value) }}" class="dropdown-menu-item btn btn-color7 btn-md-strip btn-ripple btn-border-0" onmouseup="document.dispatchEvent(new CustomEvent('CG::BtnRipple', { detail: this}))">
        <div class="icon"><i class="fa-solid fa-registered"></i></div>
        <div class="dropdown-menu-title noto-serif-tc-black">註冊</div>
    </a>
</div>
@endguest
@auth
<div id="dropdown-status2" class="dropdown-menu !hidden">
    <a href="{{ route(RouteNameField::PageProfile->value) }}" class="dropdown-menu-item btn btn-color7 btn-md-strip btn-ripple btn-border-0" onmouseup="document.dispatchEvent(new CustomEvent('CG::BtnRipple', { detail: this}))">
        <div class="icon"><i class="fa-solid fa-user"></i></div>
        <div class="dropdown-menu-title noto-serif-tc-black">個人檔案</div>
    </a>
    <a href="{{ route(RouteNameField::PageMembers->value) }}" class="dropdown-menu-item btn btn-color7 btn-md-strip btn-ripple btn-border-0" onmouseup="document.dispatchEvent(new CustomEvent('CG::BtnRipple', { detail: this}))">
        <div class="icon"><i class="fa-solid fa-table-list"></i></div>
        <div class="dropdown-menu-title noto-serif-tc-black">會員管理</div>
    </a>
    <a href="{{ route(RouteNameField::PageLogout->value) }}" class="dropdown-menu-item btn btn-color7 btn-md-strip btn-ripple btn-border-0" onmouseup="document.dispatchEvent(new CustomEvent('CG::BtnRipple', { detail: this}))">
        <div class="icon"><i class="fa-solid fa-right-from-bracket"></i></div>
        <div class="dropdown-menu-title noto-serif-tc-black">登出</div>
    </a>
</div>
@endauth

@if(\Illuminate\Support\Facades\Route::current()->getName() === RouteNameField::PageHome->value ||
    \Illuminate\Support\Facades\Route::current()->getName() === RouteNameField::PageHome2->value ||
    \Illuminate\Support\Facades\Route::current()->getName() === RouteNameField::PageShopItem->value ||
    \Illuminate\Support\Facades\Route::current()->getName() === RouteNameField::PageSearchShopItem->value)
    <div class="btn btn-ripple btn-circle btn-color7 floating-button ct" data-fn="toggleable" data-whenscreenless="-1"
         data-whenscrolltopmore="-1" data-mode="once" data-target="#filter-bar" data-status="on" data-animation="true"
         data-animationstat1="closeing" data-animationstat1-1="filter-bar-on" data-animationstat2="opening"
         data-animationstat2-1="filter-bar-off" data-animationduration="2000">
        <div class="justify-center items-center flex text-2xl w-full h-full">
            <i class="fa-solid fa-filter"></i>
        </div>
    </div>
@endif --}}
