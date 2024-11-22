@php use App\Models\Inventory; @endphp
@php use App\View\Components\ShopItemPageOption; @endphp
@php use App\Lib\Inventory\EInventoryStatus; @endphp
@php
    /**
     * @var Inventory $shopItem
     * @var ShopItemPageOption $shopItemOption
     */
@endphp
<main class="item">
    <div class="container2">
        <x-FilterBar :maxPrice="$shopItemOption->getMaxPrice()" :minPrice="$shopItemOption->getMinPrice()"></x-FilterBar>
        <div class="carousel-content">
            <div id="carouselSticky" data-topspacing="{{ $shopItemOption->getTopSpaceing() }}"
                 data-stickysize="{{ $shopItemOption->getStickysize() }}" data-margin-top="100" data-sticky-for="1027"
                 class="carousels">
                <div class="carousel-view">
                    <div class="carousel-image lazyImg placeholder placeholder-16-9" data-index="0"
                         data-src="{{ $shopItem->image_url->getImage(0)->getUri() }}"></div>
                    <div class="carousel-info-box" data-on="false">
                        <i class="carousel-info-icon fa-solid fa-info"></i>
                        <div class="!hidden carousel-title">{{ $shopItem->name }}</div>
                        <div class="!hidden carousel-subtitle">{{ $shopItem->description }}</div>
                        <div class="!hidden carousel-close-btn btn btn-circle btn-ripple btn-warning">
                            <i class="fa-solid fa-x"></i>
                        </div>
                    </div>
                </div>
                <div class="carousel-control">
                    <div class="carousel-zoom btn btn-circle btn-ripple btn-color7"><i
                            class="fa-solid fa-magnifying-glass-plus"></i></div>
                    <div class="carousel-zoom-chunk lazyImg hidden"
                         data-src="{{ $shopItem->image_url->getImage(0)->getUri() }}"></div>
                    <div class="carousel-image-list">
                        @foreach($shopItem->image_url->getImages() as $item)
                            <div class="carousel-image-cube lazyImg placeholder rippleable"
                                 data-title="{{ $item->getTitle() }}"
                                 data-subtitle="{{ $item->getDescription() }}" data-src="{{ $item->getUri() }}">
                                <div class="ripple">&nbsp;</div>
                            </div>
                        @endforeach
                    </div>
                    <div class="carousel-left btn btn-circle btn-ripple btn-color3"><i
                            class="fa-solid fa-chevron-left"></i></div>
                    <div class="carousel-right btn btn-circle btn-ripple btn-color3"><i
                            class="fa-solid fa-chevron-right"></i></div>
                </div>
            </div>
        </div>
        <div class="shop-item-content">
            <div class="field-item">
                <div class="field-row">
                    <div class="field-col">
                        <h2 class="title">
                            {{ $shopItem->name }}
                        </h2>
                        <p class="description">
                            {{ $shopItem->description }}
                        </p>
                    </div>
                </div>
                <div class="field-row">
                    <div class="field-col">
                        <h2 class="tag-title noto-serif-tc-medium">標籤</h2>
                        <div class="tags">
                            @foreach($shopItem->tags as $tag)
                                <div class="btn btn-ripple btn-pill tag btn-color3 text-white noto-serif-tc-medium">
                                    {{ $tag }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="field-row price-status-row bg-yellow-100">
                    <div class="field-col-half flex justify-start items-center">
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
                        <h2 class="status noto-serif-tc-medium SI-{{ $shopItem->status->name }}">{{ $shopItem->status }}</h2>
                    </div>
                    <div class="field-col-half flex justify-end items-center right">
                        <div class="price-label">
                            @if(in_array($shopItem->status->value, $allowShowPriceTypes))
                                @if($shopItem->last_price !== -1.0)
                                    <div class="last-price">
                                        <del>${{ number_format($shopItem->last_price, 2, '.', ',') }}</del>
                                    </div>
                                    <b>
                                        @if($shopItem->price === 0.0)
                                            <div class="noto-serif-tc-black text-center">免費</div>
                                        @else
                                            <div>${{ number_format($shopItem->price, 2, '.', ',') }}</div>
                                        @endif
                                    </b>
                                @else
                                    <b>
                                        @if($shopItem->price === 0.0)
                                            <div class="noto-serif-tc-black text-center">免費</div>
                                        @else
                                            <div>${{ number_format($shopItem->price, 2, '.', ',') }}</div>
                                        @endif
                                    </b>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="field-item">
                <div class="field-row bg-color1 ring-1 ring-slate-200">
                    <div class="field-col-full">
                        <div class="Specification-title noto-serif-tc-medium">規格</div>
                        <div class="Specification-label">
                            <select name="Specification" class="form-solid w-full">
                                <option value="1">黑色</option>
                                <option value="2">白色</option>
                                <option value="3">黃色</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="field-row !p-0">
                    <div class="field-col-full overflow-x-auto">
                        <div class="shop-item-action">
                            <button
                                class="buy-now btn btn-ripple btn-md-strip noto-serif-tc-black btn-success btn-pill">
                                立即下單
                            </button>
                            <button class="min-w-12 btn btn-error btn-circle tooltip-gen tooltip-error break-keep ct"
                                    data-tooltip="追蹤"
                                    data-direction="tooltip-bottom" data-fn="toggleable" data-mode="twin" data-target=""
                                    data-statuson="#heart-on" data-statusoff="#heart-off" data-status="off">
                                <svg id="heart-on" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                    <!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                    <path
                                        d="M225.8 468.2l-2.5-2.3L48.1 303.2C17.4 274.7 0 234.7 0 192.8l0-3.3c0-70.4 50-130.8 119.2-144C158.6 37.9 198.9 47 231 69.6c9 6.4 17.4 13.8 25 22.3c4.2-4.8 8.7-9.2 13.5-13.3c3.7-3.2 7.5-6.2 11.5-9c0 0 0 0 0 0C313.1 47 353.4 37.9 392.8 45.4C462 58.6 512 119.1 512 189.5l0 3.3c0 41.9-17.4 81.9-48.1 110.4L288.7 465.9l-2.5 2.3c-8.2 7.6-19 11.9-30.2 11.9s-22-4.2-30.2-11.9zM239.1 145c-.4-.3-.7-.7-1-1.1l-17.8-20-.1-.1s0 0 0 0c-23.1-25.9-58-37.7-92-31.2C81.6 101.5 48 142.1 48 189.5l0 3.3c0 28.5 11.9 55.8 32.8 75.2L256 430.7 431.2 268c20.9-19.4 32.8-46.7 32.8-75.2l0-3.3c0-47.3-33.6-88-80.1-96.9c-34-6.5-69 5.4-92 31.2c0 0 0 0-.1 .1s0 0-.1 .1l-17.8 20c-.3 .4-.7 .7-1 1.1c-4.5 4.5-10.6 7-16.9 7s-12.4-2.5-16.9-7z"/>
                                </svg>
                                <svg id="heart-off" class="hidden" xmlns="http://www.w3.org/2000/svg"
                                     viewBox="0 0 512 512">
                                    <!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                    <path
                                        d="M47.6 300.4L228.3 469.1c7.5 7 17.4 10.9 27.7 10.9s20.2-3.9 27.7-10.9L464.4 300.4c30.4-28.3 47.6-68 47.6-109.5v-5.8c0-69.9-50.5-129.5-119.4-141C347 36.5 300.6 51.4 268 84L256 96 244 84c-32.6-32.6-79-47.5-124.6-39.9C50.5 55.6 0 115.2 0 185.1v5.8c0 41.5 17.2 81.2 47.6 109.5z"/>
                                </svg>
                            </button>
                            <button
                                class="buy-now2 btn btn-ripple btn-md-strip noto-serif-tc-black btn-warning btn-pill">
                                加入購物車
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="field-item">
                <div class="field-row !p-0">
                    <div class="field-col-full">
                        <h2 class="details noto-serif-tc-medium">產品更多細節</h2>
                        <div class="table-details">
                            <table class="table table-row-hover table-striped">
                                <thead>
                                <tr>
                                    <th align="left">標籤</th>
                                    <th align="left">數值</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>核心數</td>
                                    <td>12</td>
                                </tr>
                                <tr>
                                    <td>瓦數</td>
                                    <td>1000W</td>
                                </tr>
                                <tr>
                                    <td>內顯</td>
                                    <td>是</td>
                                </tr>
                                <tr>
                                    <td>超頻</td>
                                    <td>是</td>
                                </tr>
                                <tr>
                                    <td>TDP</td>
                                    <td>125W</td>
                                </tr>
                                <tr>
                                    <td>平台</td>
                                    <td>AMD</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            {{--
            <div class="field-item">
                <div class="field-row">
                    <div class="field-col">

                    </div>
                </div>
            </div>
            --}}
        </div>
    </div>
</main>
