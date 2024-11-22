@php use App\Lib\Inventory\InventoryImage; @endphp
@php use App\Lib\Utils\Utils; @endphp
@php use App\Lib\Inventory\EInventoryStatus; @endphp
@php use App\Lib\Utils\RouteNameField; @endphp
<x-pagination :i18-n="$i18N" :elements="$inventorys" :nopaginationframe="1" :header-page-action="true">
    <div class="shop-list2">
        @foreach($inventorys as $key => $value)
            @php
                $add_id = "si-".\Illuminate\Support\Str::random("8");
                $reduce_id = "si-".\Illuminate\Support\Str::random("8");
                $source = json_encode($value);
                $source2 = json_encode(["id" => $value["id"]]);
                /**
                 * @var InventoryImage $image_url
                 */
                $image_url = $value->image_url;
            @endphp
            <div class="shop-item-padding2">
                <div class="shop-item2 SI" data-add="#{{ $add_id }}" data-reduce="#{{ $reduce_id }}"
                     data-source="{{ $source }}">
                    <div class="image lazyImg placeholder placeholder-16-9 ct" data-fn="popover"
                         data-source="{{ $source2 }}" data-target="#{{ $popoverid }}" data-placeholderdelay="100"
                         data-src="{{ $image_url->getImage(0)->getUri() }}"></div>
                    <div class="content">
                        <div class="title">{{ $value->name }}</div>
                        <div class="description">{{ $value->brand }}</div>
                        <div class="title-btn">
                            <div class="btn btn-ripple btn-color7">加入購物車</div>
                        </div>
                    </div>
                    <div class="tags">
                        @foreach($value->tags as $tag)
                            @php
                                $randomHexColor = Utils::getRandomHexColor();
                                $contrastColor = Utils::getContrastColor($randomHexColor);
                            @endphp
                            <div
                                class="btn btn-ripple btn-pill tag btn-color3 text-white" {{--style="background: {{ $randomHexColor }};color: {{ $contrastColor }};border-color: {{ $randomHexColor }};"--}}>{{ $tag }}</div>
                        @endforeach
                    </div>
                    @php
                        /**
                         * @var EInventoryStatus[] $allowShowPriceTypes
                         */
                        $allowShowPriceTypes = [
                            EInventoryStatus::In_stock->value,
                            EInventoryStatus::In_store_only->value,
                            EInventoryStatus::Limited_availability->value,
                            EInventoryStatus::Online_only->value,
                            EInventoryStatus::Pre_order->value,
                            EInventoryStatus::Pre_sale->value,
                        ];
                    @endphp
                    <div class="footer-label">
                        <h2 class="status noto-serif-tc-medium SI-{{ $value->status->name }}">{{ $value->status }}</h2>
                    </div>
                    <div class="action-bar">
                        <div class="price-label playwrite-hr-400">
                            @if($value->last_price !== -1.0)
                                <div class="mb-3 last-price">
                                    <del>${{ number_format($value->last_price, 2, '.', ',') }}</del>
                                </div>
                                <a class="after:-bottom-3 tooltip tooltip-bottom-line tooltip-black tooltip-gen tooltip-bottom-left-to-right"
                                   data-direction="tooltip-top"
                                   data-htmlable="true"
                                   href="{{ route(RouteNameField::PageShopItem->value, ['id'=> $value->id ]) }}"
                                   @if($value->price === 0.0)
                                       data-tooltip="<span class='break-keep'>點我查看商品</span> 免費">
                                    <div class="noto-serif-tc-black text-lg text-center">免費</div>
                                    @else
                                        data-tooltip="<span class='break-keep'>點我查看商品</span>
                                        ${{ number_format($value->price, 2, '.', ',') }}">
                                        <div>${{ number_format($value->price, 2, '.', ',') }}</div>
                                    @endif
                                </a>
                            @else
                                <a class="after:-bottom-3 tooltip tooltip-bottom-line tooltip-black tooltip-gen tooltip-bottom-left-to-right"
                                   data-direction="tooltip-top"
                                   data-htmlable="true"
                                   href="{{ route(RouteNameField::PageShopItem->value, ['id'=> $value->id ]) }}"
                                   @if($value->price === 0.0)
                                       data-tooltip="<span class='break-keep'>點我查看商品</span> 免費">
                                    <div class="noto-serif-tc-black text-lg text-center">免費</div>
                                    @else
                                        data-tooltip="<span class='break-keep'>點我查看商品</span>
                                        ${{ number_format($value->price, 2, '.', ',') }}">
                                        <div>${{ number_format($value->price, 2, '.', ',') }}</div>
                                    @endif
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</x-pagination>
