@php
    // https://github.com/yajra/laravel-datatables-docs/tree/10.0
@endphp
<table class="datatable table table-row-hover table-striped w-full"
       @if($tableOption->isScroll())
       data-cgscroller="{{ $tableOption->getScrollerJson() }}"
       @endif
       data-cgpopover="{{ $tableOption->getPopover() }}"
       data-cgpaging="{{ $tableOption->getPaging() }}"
       data-cgscrolly="{{ $tableOption->getScrollY() }}"
       data-cgsearching="{{ $tableOption->isSearching() }}"
       data-cgordering="{{ $tableOption->isOrdering() }}"
       data-cgselect="{{ $tableOption->isSelect() }}"
       data-cgresponsive="{{ $tableOption->isResponsive() }}"
       data-cgcaption="{{ $tableOption->getTitle() }}"
       data-cgdatatype="{{ $tableOption->getDataType() }}"
       data-cgdata="{{ $tableOption->getDataJSON() }}"
       data-cgfixedtable="{{ $tableOption->getfixedJson() }}"
       data-cgcolumns="{{ $tableOption->getColumnsJSON() }}">
</table>
