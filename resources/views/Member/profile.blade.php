@vite(['resources/css/profile.css', 'resources/js/profile.js'])
@use (App\Lib\I18N\I18N;use App\Lib\Server\CSRF;use App\Lib\Utils\RouteNameField;use App\View\Components\PopoverOptions)
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

@endphp
@extends('layouts.default')
@section('title', "個人資料  |  ".\Illuminate\Support\Facades\Config::get('app.name'))
@section('content')
    <x-scroll-indicator indicator-target="body"></x-scroll-indicator>
    <main class="container1">
        @env('local')
        @endenv
        <div class="p-5">
            <h1 class="my-2 text-2xl font-bold noto-serif-tc-black tracking-widest"><i class="fa-solid fa-user"></i>&nbsp;個人資料
            </h1>
            <div class="section">
                <div class="profile-grid-list">
                    @php
                        $member=\Illuminate\Support\Facades\Auth::user();
                    @endphp
                    <div class="item">
                        <div class="col fixed1">ID</div>
                        <div class="col">{{$member->id}}</div>
                    </div>
                    <div class="item">
                        <div class="col fixed1">帳號</div>
                        <div class="col">{{$member->username}}</div>
                    </div>
                    <div class="item">
                        <div class="col fixed1">電子郵件</div>
                        <div class="col">{{$member->email}}
                            @php
                                $emailpopover = new PopoverOptions();
                            @endphp
                            <x-popover
                                btn-class-list="btn btn-color7 btn-ripple"
                                popover-btn-message="編輯"
                                :popover-options="$emailpopover"
                                class="!min-w-[320px] xxl:!w-8/12 emailPopover1"
                                popover-title="編輯電子信箱">
                                <form method="POST" onsubmit="return false;">
                                    <button id="sendMailVerifyCode"
                                            type="button"
                                            class="btn btn-ripple btn-color7 btn-max btn-center ct tippyer" data-zindex="10000" data-placement="auto" data-trigger="manual" data-theme="danger" data-htmlable="true" data-content="<li class='flex flex-nowrap'>🌟請先點擊發送信件</li>"
                                            data-fn="profile.email.sendMailVerifyCode"
                                            data-token="{{(new CSRF("profile.email.sendMailVerifyCode"))->get()}}"
                                            data-target="#sendMailVerifyCodeResult"
                                            data-marksendstatus="#MailCatcher"
                                    >發送驗證碼【驗證身份】
                                    </button>
                                    <div id="sendMailVerifyCodeResult"></div>
                                    <input type="hidden" name="_token" id="csrf_token" value="{{csrf_token()}}">
                                    <div id="MailVerifyInput"
                                         class="form-row-nowarp sm:!flex-wrap xs:!flex-wrap us:!flex-wrap mt-5">
                                        <label for="MailCatcher"
                                               class="xxl:w-1/12 xl:w-2/12 lg:w-2/12 md:w-2/12 footer:w-3/12 sm:w-full xs:w-full us:w-full flex justify-start items-center">驗證身份</label>
                                        <input id="MailCatcher"
                                               class="block form-solid xxl:w-9/12 xl:w-8/12 lg:w-8/12 md:w-8/12 footer:w-6/12 sm:w-full xs:w-full us:w-full tippyer" data-zindex="10000" data-placement="auto" data-trigger="manual" data-theme="light" data-htmlable="true" data-content="<li class='flex flex-nowrap'>⭕必填項目</li><li class='flex flex-nowrap'>📨 正確的驗證碼</li><li class='flex flex-nowrap'>❌最大的長度為5</li>"
                                               type="text" placeholder="填入驗證身份用途的驗證碼" minlength="5"
                                               autocomplete="off" required>
                                        <div
                                            class="footer:px-5 xxl:w-2/12 xl:w-2/12 lg:w-2/12 md:w-2/12 footer:w-3/12 sm:w-full footer:mt-0 xs:w-full sm:mt-5 xs:mt-5 us:w-full us:mt-5 sm:px-0">
                                            <button type="button"
                                                    id="FirstEmailVerifyCode"
                                                    class="btn btn-max btn-center btn-color7 btn-ripple ct"
                                                    data-fn="profile.email.verifyCode"
                                                    data-token="{{(new CSRF("profile.email.verifyCode"))->get()}}"
                                                    data-target="#MailCatcher"
                                                    data-action="#MailVerifyInput"
                                                    data-action1="#sendMailVerifyCode"
                                                    data-action2="#sendMailVerifyCodeResult"
                                                    data-action3="#email"
                                                    data-action4="#verification"
                                            >驗證
                                            </button>
                                        </div>
                                    </div>
                                    <div id="newMailInput"
                                         class="form-row-nowarp sm:!flex-wrap xs:!flex-wrap us:!flex-wrap mt-5">
                                        <label for="email"
                                               class="xxl:w-1/12 xl:w-2/12 lg:w-2/12 md:w-2/12 footer:w-3/12 sm:w-full xs:w-full us:w-full flex justify-start items-center">電子信箱</label>
                                        <input id="email"
                                               class="block form-solid xxl:w-9/12 xl:w-8/12 lg:w-8/12 md:w-8/12 footer:w-6/12 sm:w-full xs:w-full us:w-full tippyer" data-zindex="10000" data-placement="auto" data-trigger="manual" data-theme="light" data-htmlable="true" data-content="<li class='flex flex-nowrap'>⭕必填項目</li><li class='flex flex-nowrap'>🌟獨一無二電子信箱</li><li class='flex flex-nowrap'>❌最大的長度為255</li>" data-method="email"  name="email" type="email" maxlength="255" placeholder="填入新的電子郵件" autocomplete="new-email" disabled required>
                                        <div
                                            class="footer:px-5 xxl:w-2/12 xl:w-2/12 lg:w-2/12 md:w-2/12 footer:w-3/12 sm:w-full footer:mt-0 xs:w-full sm:mt-5 xs:mt-5 us:w-full us:mt-5 sm:px-0">
                                            <button type="button"
                                                    class="btn btn-max btn-center btn-color7 btn-ripple ct"
                                                    data-fn="profile.email.newMailVerifyCode"
                                                    data-token="{{(new CSRF("profile.email.newMailVerifyCode"))->get()}}"
                                                    data-target="#newMailVerifyCodeResult"
                                                    data-data="#email"
                                                    data-needfinish="#sendMailVerifyCode"
                                            >發送
                                            </button>
                                        </div>
                                    </div>
                                    <div id="newMailVerifyCodeResult" class="mt-5"></div>
                                    <div class="form-row-nowarp mt-5 xs:!flex-wrap sm:!flex-wrap us:!flex-wrap">
                                        <label for="verification"
                                               class="xxl:w-1/12 xl:w-2/12 lg:w-2/12 md:w-2/12 footer:w-3/12 sm:w-full xs:w-full us:w-full flex justify-start items-center">驗證碼</label>
                                        <input id="verification"
                                               class="block form-solid xxl:w-11/12 xl:w-10/12 lg:w-10/12 md:w-10/12 footer:w-9/12 sm:w-full xs:w-full us:w-full tippyer" data-zindex="10000" data-placement="auto" data-trigger="manual" data-theme="light" data-htmlable="true" data-content="<li class='flex flex-nowrap'>⭕必填項目</li><li class='flex flex-nowrap'>📨 正確的驗證碼</li><li class='flex flex-nowrap'>❌最大的長度為5</li>"
                                               type="text" minlength="5" placeholder="填入新的電子郵件寄送的驗證碼"
                                               name="verification" autocomplete="off" disabled required>
                                    </div>
                                    <button type="button"
                                            class="mt-5 btn btn-ripple btn-max btn-color7 btn-center ct"
                                            data-fn="profileUpdateEmail"
                                            data-target=".emailPopover1"
                                            data-value1="#verification"
                                            data-value2="#email"
                                            data-value3="#sendMailVerifyCodeToken"
                                            data-value4="#sendMailVerifyCode"
                                            data-result="#profileUpdateEmailResult"
                                            data-token="{{(new CSRF("profile.profilepost"))->get()}}"
                                            data-method="email"
                                    >更改電子信箱
                                    </button>
                                    <div id="profileUpdateEmailResult" class="mt-5"></div>
                                </form>
                            </x-popover>
                        </div>
                    </div>
                    <div class="item">
                        <div class="col fixed1 value">密碼</div>
                        <div class="col">*****************
                            @php
                                $passwordpopover = new PopoverOptions();
                            @endphp
                            <x-popover
                                btn-class-list="btn btn-color7 btn-ripple"
                                popover-btn-message="編輯"
                                :popover-options="$passwordpopover"
                                class="xxl:!w-7/12 password-popover"
                                popover-title="編輯密碼">
                                <form action="" method="POST">
                                    <button id="sendMailVerifyCode1"
                                            type="button"
                                            class="btn btn-ripple btn-color7 btn-max btn-center ct"
                                            data-fn="profile.password.sendMailVerifyCode"
                                            data-token="{{(new CSRF("profile.password.sendMailVerifyCode"))->get()}}"
                                            data-target="#sendMailVerifyCodeResult1"
                                    >發送驗證碼【驗證身份】
                                    </button>
                                    <div id="sendMailVerifyCodeResult1" class="mt-5"></div>
                                    <div id="MailVerifyInput1"
                                         class="form-row-nowarp sm:!flex-wrap xs:!flex-wrap us:!flex-wrap mt-5">
                                        <label for="MailCatcher1"
                                               class="xxl:w-1/12 xl:w-2/12 lg:w-2/12 md:w-2/12 footer:w-3/12 sm:w-full xs:w-full us:w-full flex justify-start items-center">驗證身份</label>
                                        <input id="MailCatcher1"
                                               class="block form-solid xxl:w-9/12 xl:w-8/12 lg:w-8/12 md:w-8/12 footer:w-6/12 sm:w-full xs:w-full us:w-full"
                                               type="text" placeholder="填入驗證身份用途的驗證碼" minlength="5"
                                               autocomplete="off" required>
                                        <div
                                            class="footer:px-5 xxl:w-2/12 xl:w-2/12 lg:w-2/12 md:w-2/12 footer:w-3/12 sm:w-full footer:mt-0 xs:w-full sm:mt-5 xs:mt-5 us:w-full us:mt-5 sm:px-0">
                                            <button type="button"
                                                    class="btn btn-max btn-center btn-color7 btn-ripple ct"
                                                    data-fn="profile.password.verifyCode"
                                                    data-token="{{(new CSRF("profile.password.verifyCode"))->get()}}"
                                                    data-target="#MailCatcher1"
                                                    data-save="#MailVerifyInput1"
                                                    data-action1="#password1"
                                                    data-action2="#password2"
                                                    data-action3="#password3"
                                                    data-action4="#sendMailVerifyCode1"
                                            >驗證
                                            </button>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="password1">現在密碼</label>
                                        <div class="form-password-group">
                                            <input id="password1" class="block form-solid front" type="password"
                                                   autocomplete="current-password" disabled
                                                   required>
                                            <div class="btn btn-ripple btn-color7 btn-border-0 back ct"
                                                 data-fn="password-toggle"
                                                 data-target="#password1"><i class="fa-regular fa-eye"></i></div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="password2">新密碼</label>
                                        <div class="form-password-group">
                                            <input id="password2" class="block form-solid front" type="password"
                                                   name="password" autocomplete="new-password" disabled
                                                   required>
                                            <div class="btn btn-ripple btn-color7 btn-border-0 back ct"
                                                 data-fn="password-toggle"
                                                 data-target="#password2"><i class="fa-regular fa-eye"></i></div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="password3">重複密碼</label>
                                        <div class="form-password-group">
                                            <input id="password3" class="block form-solid front" type="password"
                                                   name="password_confirmation" autocomplete="new-password" disabled
                                                   required>
                                            <div class="btn btn-ripple btn-color7 btn-border-0 back ct"
                                                 data-fn="password-toggle"
                                                 data-target="#password3"><i class="fa-regular fa-eye"></i></div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="method" value="password">
                                    <button type="button"
                                            class="mt-5 btn btn-ripple btn-color7 btn-md-strip ct"
                                            data-fn="profileUpdatePassword"
                                            data-method="password"
                                            data-token="{{(new CSRF('profile.profilepost'))->get()}}"
                                            data-target="#profile_password_sendMailVerifyCodeToken"
                                            data-result="#sendMailVerifyCodeResult1"
                                            data-popover=".password-popover"
                                            data-data1="#password1"
                                            data-data2="#password2"
                                            data-data3="#password3"
                                    >更改密碼
                                    </button>
                                </form>
                            </x-popover>
                        </div>
                    </div>
                    <div class="item">
                        <div class="col fixed1">電子郵件驗證時間</div>
                        <div class="col">{{$member->email_verified_at}}</div>
                    </div>
                    <div class="item">
                        <div class="col fixed1">電話</div>
                        <div class="col">
                            {{$member->phone}}
                            <div class="btn btn-color7 btn-ripple btn-md-strip">編輯</div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="col fixed1 value">可使用會員</div>
                        <div class="col">
                            <x-boolean-string-cover :value="$member->enable" :i18-n="$i18N"/>
                        </div>
                    </div>
                    <div class="item">
                        <div class="col fixed1 value">管理員</div>
                        <div class="col">
                            <x-boolean-string-cover :value="$member->administrator" :i18-n="$i18N"/>
                        </div>
                    </div>
                </div>

            </div>
            {{--  <h1 class="mt-5 my-2 text-2xl font-bold noto-serif-tc-black tracking-widest"><i
                     class="fa-solid fa-table-list"></i>&nbsp;儲存的訂單</h1>
             <div>
                 @php
                     $popover = "PW_".\Illuminate\Support\Str::random();
                     $tableOptions = new \App\View\Components\TableOption('',
                         [
                             [ "data" => 'id', "title" => "ID", "footer" => "ID" ],
                             [ "data" => 'goto', "title" => "訪問", "footer" => "訪問", "className" => 'clickable', "orderable"=> false,"searchable"=> false, ],
                             [ "data" => 'name', "title" => "名稱" , "footer" => "名稱" ],
                             [ "data" => 'priceTotal', "title" => "總金額", "footer" => "總金額" ],
                             [ "data" => 'sentToLine', "title" => "Line 客服通知", "footer" => "Line 客服通知" ],
                             [ "data" => 'countItem', "title" => "商品總數量", "footer" => "商品總數量" ],
                             [ "data" => 'expired_at', "title" => "過期時間", "footer" => "過期時間" ],
                         ],
                         'ServerSide',
                         [
                             "url" => route(RouteNameField::APICustomerOrderListPost->value),
                             "type" => 'POST',
                             "timeout" => 100000,
                             "headers" => [
                                 "X-CSRF-TOKEN" => csrf_token()
                             ]
                         ],
                         //scroll: true,
                         responsive: true,
                         //scroller: [
                         //    "displayBuffer" => 9,
                         //],
                         scrollY: 450,
                         fixedHeader: true,
                         fixedFooter: true,
                         paging: true,
                         popover: $popover,
                     );
                 @endphp
                 <x-popover-windows class="order-item-popover !hidden"
                                    popover-title="檢視詳細訂單" :id="$popover"
                                    :popover-options="new \App\View\Components\PopoverOptions()">
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
                     <iframe class="order-iframe"></iframe>
                 </x-popover-windows>
                 <x-DataTable :table-option="$tableOptions"></x-DataTable>
             </div> --}}
        </div>
    </main>
@endsection
