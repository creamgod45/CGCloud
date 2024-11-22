<?php

namespace App\View\Components;

class PopoverOptions
{
    public bool $blackBackground = true;
    public bool $blurBackground = true;
    public bool $useOfficePluginsProvider = true;

    /**
     * @param string $id
     * @param bool   $blackBackground
     * @param bool   $blurBackground
     * @param bool   $useOfficePluginsProvider
     */
    public function __construct(
        bool $blackBackground = true,
        bool $blurBackground = true,
        bool $useOfficePluginsProvider = true,
    ) {
        $this->blackBackground = $blackBackground;
        $this->blurBackground = $blurBackground;
        $this->useOfficePluginsProvider = $useOfficePluginsProvider;
    }

    /**
     * @return bool
     */
    public function isBlackBackground(): bool
    {
        return $this->blackBackground;
    }

    /**
     * @param bool $blackBackground
     *
     * @return PopoverOptions
     */
    public function setBlackBackground(bool $blackBackground): PopoverOptions
    {
        $this->blackBackground = $blackBackground;
        return $this;
    }

    /**
     * @return bool
     */
    public function isBlurBackground(): bool
    {
        return $this->blurBackground;
    }

    /**
     * @param bool $blurBackground
     *
     * @return PopoverOptions
     */
    public function setBlurBackground(bool $blurBackground): PopoverOptions
    {
        $this->blurBackground = $blurBackground;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUseOfficePluginsProvider(): bool
    {
        return $this->useOfficePluginsProvider;
    }

    /**
     * @param bool $useOfficePluginsProvider
     *
     * @return PopoverOptions
     */
    public function setUseOfficePluginsProvider(bool $useOfficePluginsProvider): PopoverOptions
    {
        $this->useOfficePluginsProvider = $useOfficePluginsProvider;
        return $this;
    }

}
