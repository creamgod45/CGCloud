@use(App\Lib\I18N\ELanguageText)
@use(Illuminate\Support\Facades\Config)
<footer>
    <div class="footer-menu-panel">
        <div class="footer-menu">
            <div class="item">
                <div class="title">社群媒體</div>
                <a class="menu-btn btn btn-ripple"><i class="fa-brands fa-facebook"></i>
                    <span>&nbsp;FaceBook</span>
                </a>
                <div class="menu-btn btn btn-ripple"><i class="fa-brands fa-x-twitter"></i><span>&nbsp;X</span></div>
                <div class="menu-btn btn btn-ripple"><i class="fa-brands fa-youtube"></i><span>&nbsp;Youtube</span></div>
                <div class="menu-btn btn btn-ripple"><i class="fa-brands fa-instagram"></i><span>&nbsp;Instagram</span></div>
                <div class="menu-btn btn btn-ripple"><i class="fa-brands fa-threads"></i><span>&nbsp;Threads</span></div>
                <div class="menu-btn btn btn-ripple"><i class="fa-solid fa-map-location-dot"></i><span>&nbsp;Google 地圖</span></div>
                <div class="menu-btn btn btn-ripple"><i class="fa-solid fa-map-location-dot"></i><span>&nbsp;Apple 地圖</span></div>
            </div>
            <div class="item">
                <div class="title">資源</div>
                <div class="menu-btn btn btn-ripple"><i class="fa-solid fa-users"></i><span>&nbsp;社群</span></div>
                <div class="menu-btn btn btn-ripple"><i class="fa-solid fa-globe"></i><span>&nbsp;合作夥伴</span></div>
                <div class="menu-btn btn btn-ripple"><i class="fa-solid fa-book-open"></i><span>&nbsp;教學</span></div>
                <div class="menu-btn btn btn-ripple"><i class="fa-brands fa-wikipedia-w"></i><span>&nbsp;維基</span></div>
                <div class="menu-btn btn btn-ripple"><i class="fa-solid fa-shield-halved"></i><span>&nbsp;隱私權</span>
                </div>
            </div>
            <div class="item">
                <div class="title">合作夥伴</div>
                <a class="menu-btn btn btn-ripple">
                    <i class="fa-solid fa-question"></i>
                    <span>&nbsp;XXX 科技</span>
                </a>
                <a class="menu-btn btn btn-ripple">
                    <i class="fa-solid fa-question"></i>
                    <span>&nbsp;原X屋</span>
                </a>
            </div>
            <div class="item">
                <div class="title">服務</div>
                <a class="menu-btn btn btn-ripple">
                    <i class="fa-solid fa-money-bill-1"></i>
                    <span>&nbsp;線上估價</span>
                </a>
                <a class="menu-btn btn btn-ripple">
                    <i class="fa-solid fa-location-dot"></i>
                    <span>&nbsp;全台據點</span>
                </a>
                <a class="menu-btn btn btn-ripple">
                    <i class="fa-solid fa-route"></i>
                    <span>&nbsp;購買流程</span>
                </a>
                <a class="menu-btn btn btn-ripple">
                    <i class="fa-brands fa-golang"></i>
                    <span>&nbsp;到府服務</span>
                </a>
                <a class="menu-btn btn btn-ripple">
                    <i class="fa-solid fa-phone-volume"></i>
                    <span>&nbsp;聯絡我們</span>
                </a>
            </div>
        </div>
        <div class="footer1">
            <div class="row">
                <div class="col">© {{ date("Y") }} {{ Config::get('app.name') }} Author:&nbsp;<a class="creamgod45" target="_blank"
                                                                                                 href="https://github.com/creamgod45">CreamGod45</a>
                </div>
                <div class="col">Power by&nbsp;<a class="laravel" href="https://laravel.com/" target="_blank">Laravel
                        10</a>.
                </div>
            </div>
        </div>
    </div>
</footer>
