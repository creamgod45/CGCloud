@vite(['resources/css/index.css', 'resources/js/index.js'])
@use (App\Lib\I18N\ELanguageText;use App\Lib\I18N\I18N;use App\Lib\Utils\Htmlv2;use App\Lib\Utils\RouteNameField;use App\Lib\Utils\Utilsv2;use App\View\Components\TableOption;use Illuminate\Http\Request;use Illuminate\Support\Facades\Config)
@php
    /***
     * @var string[] $urlParams 當前 URL 參數
     * @var array $moreParams 更多參數
     * @var I18N $i18N I18N 本地化語言系統
     * @var Request $request 請求
     * @var string $fingerprint 客戶端指紋
     * @var string $theme 主題
     * @var \App\Models\ShopConfig[] $styleConfig 系統設定
     * @var \App\Models\CustomerSaveOrder $item
     */
    $menu = false;
    $footer = false;
    $item = null;
    if(!empty($moreParams)){
        if(isset($moreParams[0]['item'])){
            $item = $moreParams[0]['item'];
        }
    }
    /**
     * @var ?\App\Models\CustomerSaveOrder $item
     **/
@endphp
@extends('layouts.default')
@section('title', "檢視儲存的訂單 | ".Config::get('app.name'))
@section('content')
    <x-scroll-indicator indicator-target="body"></x-scroll-indicator>
    <main>
        <div class="container8">
            @if($item === null)
                @php
                    $messageBag = new \Illuminate\Support\MessageBag();
                    $messageBag->add("NotFound", "沒有找到關於此紀錄的訂單明細");
                @endphp
                <x-alert type="error" :messages="$messageBag->all()" />
            @else
                <div class="grid-field">
                    <div class="grid-field-item">
                        <div class="title noto-serif-tc-black">訂單編號</div>
                        <div class="content">{{ $item->id }}</div>
                    </div>
                    <div class="grid-field-item relative">
                        <div class="title noto-serif-tc-black">訂單名稱</div>
                        <div class="content noto-serif-tc-black tippyer" data-placement="auto" data-content="{{ $item->name }}">{{ $item->name }}</div>
                    </div>
                    <div class="grid-field-item">
                        <div class="title noto-serif-tc-black">訂單總金額</div>
                        <div class="content text-right">${{ $item->priceTotal }}</div>
                    </div>
                    <div class="grid-field-item">
                        <div class="title noto-serif-tc-black">通知客服</div>
                        <div class="content">{{ $item->sentToLine ? '是' : '否' }}</div>
                    </div>
                </div>
                <div class="table-details">
                    <table class="table table-row-hover table-striped">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th nowrap>產品圖片</th>
                            <th nowrap>產品類型</th>
                            <th nowrap>產品名稱</th>
                            <th nowrap>產品價格</th>
                            <th nowrap>產品數量</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            /**
                             * @var \App\Lib\Inventory\SimpleOrderItem $inventory
                             */
                        @endphp
                        @foreach($item->itemList as $key => $inventory)
                            @php
                                $inv = $inventory->load()->getInventory();
                            @endphp
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td>
                                    <img src="{{ $inv?->image_url->getImage(0)->getUri() }}"
                                         class="size-32"
                                         alt="{{ $inv?->name }}">
                                </td>
                                <td>{{ $inv?->type }}</td>
                                <td>
                                    <span class="tippyer" data-placement="auto" data-content="{{ $inv?->description }}">
                                        {{ $inv?->name }}
                                    </span>
                                </td>
                                <td>$ {{ number_format($inv?->price * $inventory->getQuantity(), 2) }}</td>
                                <td>{{ $inventory->getQuantity() }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <span>訂單紀錄過期時間: {{ $item->expired_at }}</span>
            @endif
        </div>
    </main>
@endsection
