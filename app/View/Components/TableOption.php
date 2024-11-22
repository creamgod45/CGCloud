<?php

namespace App\View\Components;

class TableOption
{
    private string $title;
    private array $columns;
    private string $dataType;
    private array|string $data;
    private bool $responsive;
    private bool $select;
    private bool $searching;
    private bool $ordering;
    private array $scroller;
    private string $scrollY;
    private bool $scroll;
    private array $fixedHeader;
    private bool $paging;
    private string $popover;

    /**
     * @param string       $title       標題
     * @param array        $columns     欄位設定
     * @param string       $dataType    資料型態 JSON, ServerSide(ajax class 介面)
     * @param array|string $data        資料 ServerSide => [ "url", "type", "headers" ] , JSON => 準備資料
     * @param bool         $scroll      允許動態滾輪加載
     * @param bool         $responsive  允許 RWD
     * @param bool         $select      允許 checkbox
     * @param bool         $searching   允許搜尋
     * @param bool         $ordering    允許排序
     * @param array        $scroller    設定 scroller
     * @param string       $scrollY     最大滾輪顯示的高度(MAX-HEIGHT)
     * @param bool         $fixedHeader 固定表格頭
     * @param bool         $fixedFooter 固定表格尾
     * @param bool         $paging      固定表格尾
     * @param string       $popover     popover ID API
     */
    public function __construct(
        string $title,
        array $columns,
        string $dataType,
        array|string $data,
        bool $scroll = false,
        bool $responsive = true,
        bool $select = true,
        bool $searching = true,
        bool $ordering = true,
        array $scroller = [],
        string $scrollY = "",
        bool $fixedHeader = false,
        bool $fixedFooter = false,
        bool $paging = false,
        string $popover = "",
    ) {
        $this->title = $title;
        $this->columns = $columns;
        $this->dataType = $dataType;
        $this->data = $data;
        $this->responsive = $responsive;
        $this->select = $select;
        $this->searching = $searching;
        $this->ordering = $ordering;
        $this->scroller = $scroller;
        $this->scrollY = $scrollY;
        $this->scroll = $scroll;
        $this->fixedHeader = [
            "header" => $fixedHeader,
            "footer" => $fixedFooter,
        ];
        $this->paging = $paging;
        $this->popover = $popover;
    }

    /**
     * @return string
     */
    public function getPopover(): string
    {
        return $this->popover;
    }

    /**
     * @return array
     */
    public function getFixedHeader(): array
    {
        return $this->fixedHeader;
    }

    /**
     * @return string
     */
    public function getPaging(): string
    {
        return $this->paging ? "true" : "false";
    }

    public function getfixedJson()
    {
        return json_encode($this->fixedHeader);
    }

    /**
     * @return string
     */
    public function isScroll(): string
    {
        return $this->scroll ? true : false;
    }

    /**
     * @return string
     */
    public function getScrollY(): string
    {
        return $this->scrollY;
    }

    /**
     * @return array
     */
    public function getScroller(): array
    {
        return $this->scroller;
    }

    /**
     * @return string
     */
    public function getScrollerJson(): string
    {
        return json_encode($this->scroller);
    }

    /**
     * @return string
     */
    public function isSelect(): string
    {
        return $this->select ? "true" : "false";
    }

    /**
     * @return string
     */
    public function isSearching(): string
    {
        return $this->searching ? "true" : "false";
    }

    /**
     * @return string
     */
    public function isOrdering(): string
    {
        return $this->ordering ? "true" : "false";
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @return string
     */
    public function getDataType(): string
    {
        return $this->dataType;
    }

    /**
     * @return array|string
     */
    public function getData(): array|string
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function isResponsive(): string
    {
        return $this->responsive ? "true" : "false";
    }

    /**
     * @return false|string
     */
    public function getColumnsJSON(): false|string
    {
        return json_encode($this->columns);
    }


    /**
     * @return false|string
     */
    public function getDataJSON(): false|string
    {
        return json_encode($this->data);
    }


}
