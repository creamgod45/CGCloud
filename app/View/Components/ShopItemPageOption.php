<?php

namespace App\View\Components;

class ShopItemPageOption
{
    private string $mode;
    private string $topSpaceing;
    private string $stickysize;
    private float $maxPrice;
    private float $minPrice;

    /**
     * @param string $mode normal|popover
     * @param string $topSpaceing 設定 sticky 後保持多少距離
     */
    public function __construct(
        string $mode = "normal",
        string $topSpaceing = "",
        string $stickysize = "",
        float $maxPrice,
        float $minPrice,
    ) {
        $this->mode = $mode;
        $this->topSpaceing = $topSpaceing;
        $this->stickysize = $stickysize;
        $this->maxPrice = $maxPrice;
        $this->minPrice = $minPrice;
    }

    /**
     * @return float
     */
    public function getMaxPrice(): float
    {
        return $this->maxPrice;
    }

    /**
     * @return float
     */
    public function getMinPrice(): float
    {
        return $this->minPrice;
    }

    /**
     * @return string
     */
    public function getStickysize(): string
    {
        return $this->stickysize;
    }

    /**
     * @param string $stickysize
     *
     * @return ShopItemPageOption
     */
    public function setStickysize(string $stickysize): ShopItemPageOption
    {
        $this->stickysize = $stickysize;
        return $this;
    }

    /**
     * 取得 sticky 後保持多少距離
     *
     * @return string
     */
    public function getTopSpaceing(): string
    {
        return $this->topSpaceing;
    }

    /**
     * 設定 sticky 後保持多少距離
     *
     * @param string $topSpaceing
     *
     * @return ShopItemPageOption
     */
    public function setTopSpaceing(string $topSpaceing): ShopItemPageOption
    {
        $this->topSpaceing = $topSpaceing;
        return $this;
    }

    /**
     * @return string
     */
    public function getMode(): string
    {
        return $this->mode;
    }
}
