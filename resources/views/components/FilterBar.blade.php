@php
    use App\Lib\Utils\RouteNameField;use Illuminate\Support\MessageBag;if($errors===null){
        $errors = new MessageBag();
    }
@endphp
<form id="filter-bar" action="{{ route(RouteNameField::PageSearchShopItem->value) }}" method="GET"
      class="filter-bar filter-bar-off hidden form-common" data-status="off">
    <div class="filter-bar-item px-3">
        <input type="submit" class="btn btn-max btn-color7 btn-ripple btn-border-0" name="search" value="搜尋">
    </div>
    <div class="filter-bar-item px-3">
        <input type="reset" class="btn btn-max btn-color7 btn-ripple btn-border-0" name="search" value="重設輸入選項">
    </div>
    <div class="filter-bar-item p-5">
        <input class="form-solid w-full" type="search" name="q" value="{{ old('q') }}"
               placeholder="輸入關鍵字如果要搜尋多個請使用,">
    </div>
    <div class="filter-bar-item px-5">
        <div class="select-bar-query" data-textinput="false" data-layer="1">
            <div id="datalist1" class="sbq-fake-input" data-index="0">
            </div>
            <div class="sbq-float-menu !hidden">
                <div class="sbq-text-input">
                    <textarea class="sbq-search-input"></textarea>
                </div>
                <div class="sbq-select-group" data-step="4">
                    @php
                        $name = [
                            'type' => 'list',
                            'items' => [
                                [
                                    "id" => "product1",
                                    "text" => '產品1',
                                ],
                                [
                                    "id" => "product2",
                                    "text" => '產品2',
                                ],
                            ],
                        ];
                        $enableOptions = [
                            'type' => 'list',
                            'items' => [
                                [
                                    "id" => "false",
                                    "text" => '關閉',
                                ],
                                [
                                    "id" => "true",
                                    "text" => '開啟',
                                ],
                            ],
                        ];
                    @endphp
                    <div data-groupid="1" data-matches="all" data-label="產品名稱" data-input="name" data-options="{{ json_encode($name) }}"
                         class="sbq-menu-item"><i class="fa-solid fa-font"></i><span>產品名稱 name</span></div>
                    <div data-groupid="1" data-matches="all" data-label="產品說明" data-input="description"
                         class="sbq-menu-item"><i class="fa-solid fa-comment"></i><span>產品說明 description</span></div>
                    <div data-groupid="1" data-matches="all" data-label="產品品牌名" data-input="brand"
                         class="sbq-menu-item"><i class="fa-solid fa-font"></i><span>產品品牌名 brand</span></div>
                    <div data-groupid="1" data-matches="all" data-label="產品製造商零件編號" data-input="MPN"
                         class="sbq-menu-item"><i class="fa-solid fa-id-card-clip"></i><span>產品製造商零件編號 MPN</span>
                    </div>
                    <div data-groupid="1" data-matches="all" data-label="產品庫存單位編號" data-input="SKU"
                         class="sbq-menu-item"><i class="fa-solid fa-id-card-clip"></i><span>產品庫存單位編號 SKU</span>
                    </div>
                    <div data-groupid="1" data-matches="all" data-label="產品全球商品貿易項目編號" data-input="GTIN"
                         class="sbq-menu-item"><i
                            class="fa-solid fa-id-card-clip"></i><span>產品全球商品貿易項目編號</span></div>
                    <div data-groupid="1" data-matches="all" data-label="產品存貨狀態" data-input="status"
                         class="sbq-menu-item"><i class="fa-solid fa-toggle-off"></i><span>產品存貨狀態 status</span></div>
                    <div data-groupid="1" data-matches="all" data-label="產品存貨品質" data-input="quality"
                         class="sbq-menu-item"><i class="fa-solid fa-box"></i><span>產品存貨品質 quality</span></div>
                    <div data-groupid="1" data-matches="all" data-label="產品標籤" data-input="tags"
                         class="sbq-menu-item"><i class="fa-solid fa-tags"></i><span>產品標籤 tags</span></div>
                    <div data-groupid="1" data-matches="all" data-label="產品更多細節" data-input="details"
                         class="sbq-menu-item"><i class="fa-solid fa-info"></i><span>產品更多細節 details</span></div>
                    <div data-groupid="1" data-matches="all" data-label="產品啟用狀態" data-input="enable" data-options="{{ json_encode($enableOptions) }}"
                         class="sbq-menu-item"><i class="fa-solid fa-check"></i><span>產品啟用狀態 enable</span></div>

                    <div data-groupid="2" data-input="memory" data-label="記憶體(GB)" data-matches="details"
                         class="sbq-menu-item"><i class="fa-solid fa-magnifying-glass"></i><span>記憶體(GB) memory</span></div>
                    <div data-groupid="2" data-input="clock" data-label="時脈(Hz)" data-matches="details"
                         class="sbq-menu-item"><i class="fa-solid fa-magnifying-glass"></i><span>時脈(Hz) clock</span></div>
                    <div data-groupid="2" data-input="cuda" data-label="CUDA數" data-matches="details"
                         class="sbq-menu-item"><i class="fa-solid fa-magnifying-glass"></i><span>CUDA數 cuda</span></div>
                    <div data-groupid="2" data-input="cores" data-label="核心數" data-matches="details"
                         class="sbq-menu-item"><i class="fa-solid fa-magnifying-glass"></i><span>核心數 cores</span></div>
                    <div data-groupid="2" data-input="wattage" data-label="瓦數(W)" data-matches="details"
                         class="sbq-menu-item"><i class="fa-solid fa-magnifying-glass"></i><span>瓦數(W) wattage</span></div>
                    <div data-groupid="2" data-input="storage" data-label="儲存(GB)" data-matches="details"
                         class="sbq-menu-item"><i class="fa-solid fa-magnifying-glass"></i><span>儲存(GB) storage</span></div>
                    <div data-groupid="2" data-input="IO" data-label="輸入輸出(IO)" data-matches="details"
                         class="sbq-menu-item"><i class="fa-solid fa-magnifying-glass"></i><span>輸入輸出(IO) IO</span>
                    </div>
                    <div data-groupid="2" data-input="equal" data-label="等於" data-matches="all" data-action="text"
                         class="sbq-menu-item"><i class="fa-solid fa-magnifying-glass"></i><span>等於 equal</span></div>
                    <div data-groupid="2" data-input="notequal" data-label="不等於" data-matches="all"
                         data-action="text" class="sbq-menu-item"><i
                            class="fa-solid fa-magnifying-glass"></i><span>不等於 notequal</span></div>
                    <div data-groupid="2" data-input="include" data-label="包括" data-matches="all" data-action="text"
                         class="sbq-menu-item"><i class="fa-solid fa-magnifying-glass"></i><span>包括 include</span></div>
                    <div data-groupid="2" data-input="exclude" data-label="不包括" data-matches="all" data-action="text"
                         class="sbq-menu-item"><i class="fa-solid fa-magnifying-glass"></i><span>不包括 exclude</span></div>

                    <div data-groupid="3" data-input="equal" data-label="等於" data-action="text" data-matches="all"
                         class="sbq-menu-item"><i class="fa-solid fa-magnifying-glass"></i><span>等於 equal</span></div>
                    <div data-groupid="3" data-input="notequal" data-label="不等於" data-action="text"
                         data-matches="all" class="sbq-menu-item"><i
                            class="fa-solid fa-magnifying-glass"></i><span>不等於 notequal</span></div>
                    <div data-groupid="3" data-input="include" data-label="包括" data-action="text" data-matches="all"
                         class="sbq-menu-item"><i class="fa-solid fa-magnifying-glass"></i><span>包括 include</span></div>
                    <div data-groupid="3" data-input="exclude" data-label="不包括" data-action="text" data-matches="all"
                         class="sbq-menu-item"><i class="fa-solid fa-magnifying-glass"></i><span>不包括 exclude</span></div>

                    <div data-groupid="-1" data-input="none" data-label=";" data-matches="all" class="sbq-menu-item"><i
                            class="fa-solid fa-xmark"></i><span>無 none</span></div>
                    <div data-groupid="-1" data-input="and" data-label="和" data-matches="all" class="sbq-menu-item"><b>AND</b><span>和</span>
                    </div>
                    <div data-groupid="-1" data-input="or" data-label="或" data-matches="all" class="sbq-menu-item"><b>OR</b><span>或</span>
                    </div>
                    <div data-groupid="-2" data-input="text" data-label="文字" data-inputtarget="#sbq-input-text" data-matches="all" class="sbq-menu-item">
                        <i class="fa-solid fa-font"></i><input type="text" id="sbq-input-text" class="form-solid input" placeholder="輸入文字">
                    </div>
                    <div data-groupid="-2" data-input="select" data-label="下拉選單" data-inputtarget="#sbq-input-select" data-matches="all" class="sbq-menu-item">
                        <i class="fa-solid fa-square-caret-down"></i>
                        <select id="sbq-input-select" class="select2">
                            <option></option>
                        </select>
                    </div>
                </div>
            </div>
            <input type="hidden" id="sbq_advanced_search" name="advanced_search">
            <div class="btn-group w-full ct mt-4" data-fn="datalist_selector" data-target="#datalist1"
                 data-next="#next1" data-prev="#perv1" data-lists="#datalist1">
                <div id="perv1" class="btn btn-border-0 btn-ripple btn-ok">
                    <i class="fa-solid fa-chevron-left"></i>
                </div>
                <button type="button" class="btn btn-border-0 btn-ripple space-x-1 btn-color7 btn-max"><i
                        class="fa-solid fa-rotate-left"></i><span>重設搜尋欄位</span></button>
                <div id="next1" class="btn btn-border-0 btn-ripple btn-warning">
                    <i class="fa-solid fa-chevron-right"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="filter-bar-item px-5">
        @if ($errors->any())
            <x-alert type="danger" :messages="$errors->all()"/>
        @endif
    </div>
    <div class="noto-serif-tc-bold pt-16 ps-2">價格</div>
    <div class="filter-bar-item price-slider ps-2">
        <div class="flex ms-4 space-x-2 form-solid-checkbox-grp">
            <input id="price_filter" class="form-solid-checkbox" type="checkbox" name="price_filter" value="true">
            <label for="price_filter" class="form-solid-checked-emoji"><i class="fa-solid fa-check"></i></label>
            <label for="price_filter" class="noto-serif-tc-bold">過濾價錢</label>
        </div>
        <input id="slider" type="text" class="js-range-slider" name="my_range" value=""
               data-type="double"
               data-step="10"
               data-min="0"
               data-max="{{ $maxPrice }}"
               data-from="{{ $minPrice }}"
               data-to="{{ $maxPrice }}"
               data-extra-classes="mx-5"
               data-grid="true">
    </div>
    <div class="filter-bar-item ps-2">
        <div class="flex justify-center items-center space-x-3">
            <label for="min_price" class="noto-serif-tc-bold">最低</label>
            <input id="min_price" value="{{ $minPrice }}" readonly class="min_price price-input form-solid validate"
                   data-method="number" data-step="10.00" data-numberofdigits="2" data-negative="false" type="text"
                   min="0.00" name="min_price">
        </div>
        <div class="splitter-horizontal"></div>
        <div class="flex justify-center items-center space-x-3">
            <label for="max_price" class="noto-serif-tc-bold">最高</label>
            <input id="max_price" value="{{ $maxPrice }}" readonly data-step="10.00"
                   class="max_price price-input form-solid validate" data-method="number" data-numberofdigits="2"
                   data-negative="false" type="text" min="0.00" name="max_price">
        </div>
    </div>
    {{-- <div class="noto-serif-tc-bold ps-2">標籤</div>
    <div class="filter-bar-item ps-2">
        <div class="ps-5">
            <div class="flex space-x-2 form-solid-checkbox-grp">
                <input id="tag1" class="form-solid-checkbox" type="checkbox" name="tags[]" value="店長推薦">
                <label for="tag1" class="form-solid-checked-emoji"><i class="fa-solid fa-check"></i></label>
                <label for="tag1" class="noto-serif-tc-bold">店長推薦</label>
            </div>
            <div class="flex space-x-2 form-solid-checkbox-grp">
                <input id="tag2" class="form-solid-checkbox" type="checkbox" name="tags[]" value="新品推薦">
                <label for="tag2" class="form-solid-checked-emoji"><i class="fa-solid fa-check"></i></label>
                <label for="tag2" class="noto-serif-tc-bold">新品推薦</label>
            </div>
            <div class="flex space-x-2 form-solid-checkbox-grp">
                <input id="tag3" class="form-solid-checkbox" type="checkbox" name="tags[]" value="清倉">
                <label for="tag3" class="form-solid-checked-emoji"><i class="fa-solid fa-check"></i></label>
                <label for="tag3" class="noto-serif-tc-bold">清倉</label>
            </div>
            <div class="flex space-x-2 form-solid-checkbox-grp">
                <input id="tag4" class="form-solid-checkbox" type="checkbox" name="tags[]" value="福利品">
                <label for="tag4" class="form-solid-checked-emoji"><i class="fa-solid fa-check"></i></label>
                <label for="tag4" class="noto-serif-tc-bold">福利品</label>
            </div>
            <div class="flex space-x-2 form-solid-checkbox-grp">
                <input id="tag5" class="form-solid-checkbox" type="checkbox" name="tags[]" value="本店限定">
                <label for="tag5" class="form-solid-checked-emoji"><i class="fa-solid fa-check"></i></label>
                <label for="tag5" class="noto-serif-tc-bold">本店限定</label>
            </div>
            <div class="flex space-x-2 form-solid-checkbox-grp">
                <input id="tag6" class="form-solid-checkbox" type="checkbox" name="tags[]" value="超商取貨">
                <label for="tag6" class="form-solid-checked-emoji"><i class="fa-solid fa-check"></i></label>
                <label for="tag6" class="noto-serif-tc-bold">超商取貨</label>
            </div>
            <div class="flex space-x-2 form-solid-checkbox-grp">
                <input id="tag7" class="form-solid-checkbox" type="checkbox" name="tags[]" value="宅配到家">
                <label for="tag7" class="form-solid-checked-emoji"><i class="fa-solid fa-check"></i></label>
                <label for="tag7" class="noto-serif-tc-bold">宅配到家</label>
            </div>
        </div>
    </div>
    <div class="noto-serif-tc-bold ps-2">種類</div>
    <div class="filter-bar-item ps-2">
        <div class="ps-5">
            <div class="flex space-x-2 form-solid-checkbox-grp">
                <input id="types1" class="form-solid-checkbox" type="checkbox" name="types[]" value="筆電">
                <label for="types1" class="form-solid-checked-emoji"><i class="fa-solid fa-check"></i></label>
                <label for="types1" class="noto-serif-tc-bold">筆電</label>
            </div>
            <div class="flex space-x-2 form-solid-checkbox-grp">
                <input id="types2" class="form-solid-checkbox" type="checkbox" name="types[]" value="套裝機">
                <label for="types2" class="form-solid-checked-emoji"><i class="fa-solid fa-check"></i></label>
                <label for="types2" class="noto-serif-tc-bold">套裝機</label>
            </div>
            <div class="flex space-x-2 form-solid-checkbox-grp">
                <input id="types3" class="form-solid-checkbox" type="checkbox" name="types[]" value="處理器(CPU)">
                <label for="types3" class="form-solid-checked-emoji"><i class="fa-solid fa-check"></i></label>
                <label for="types3" class="noto-serif-tc-bold">處理器(<span class="roboto-bold">CPU</span>)</label>
            </div>
            <div class="flex space-x-2 form-solid-checkbox-grp">
                <input id="types4" class="form-solid-checkbox" type="checkbox" name="types[]" value="主機板(MB)">
                <label for="types4" class="form-solid-checked-emoji"><i class="fa-solid fa-check"></i></label>
                <label for="types4" class="noto-serif-tc-bold">主機板(<span class="roboto-bold">MB</span>)</label>
            </div>
            <div class="flex space-x-2 form-solid-checkbox-grp">
                <input id="types5" class="form-solid-checkbox" type="checkbox" name="types[]" value="記憶體(RAM)">
                <label for="types5" class="form-solid-checked-emoji"><i class="fa-solid fa-check"></i></label>
                <label for="types5" class="noto-serif-tc-bold">記憶體(<span class="roboto-bold">RAM</span>)</label>
            </div>
            <div class="flex space-x-2 form-solid-checkbox-grp">
                <input id="types6" class="form-solid-checkbox" type="checkbox" name="types[]" value="儲存裝置(Storage)">
                <label for="types6" class="form-solid-checked-emoji"><i class="fa-solid fa-check"></i></label>
                <label for="types6" class="noto-serif-tc-bold">儲存裝置(<span
                        class="roboto-bold">Storage</span>)</label>
            </div>
            <div class="flex space-x-2 form-solid-checkbox-grp">
                <input id="types7" class="form-solid-checkbox" type="checkbox" name="types[]" value="固態硬碟(SSD)">
                <label for="types7" class="form-solid-checked-emoji"><i class="fa-solid fa-check"></i></label>
                <label for="types7" class="noto-serif-tc-bold">固態硬碟(<span class="roboto-bold">SSD</span>)</label>
            </div>
            <div class="flex space-x-2 form-solid-checkbox-grp">
                <input id="types8" class="form-solid-checkbox" type="checkbox" name="types[]" value="機械硬碟(HHD)">
                <label for="types8" class="form-solid-checked-emoji"><i class="fa-solid fa-check"></i></label>
                <label for="types8" class="noto-serif-tc-bold">機械硬碟(<span class="roboto-bold">HHD</span>)</label>
            </div>
            <div class="flex space-x-2 form-solid-checkbox-grp">
                <input id="types9" class="form-solid-checkbox" type="checkbox" name="types[]" value="顯示卡(GPU)">
                <label for="types9" class="form-solid-checked-emoji"><i class="fa-solid fa-check"></i></label>
                <label for="types9" class="noto-serif-tc-bold">顯示卡(<span class="roboto-bold"><span
                            class="roboto-bold">GPU</span></span>)</label>
            </div>
        </div>
    </div> --}}
</form>
