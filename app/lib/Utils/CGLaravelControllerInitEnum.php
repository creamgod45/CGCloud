<?php

namespace App\Lib\Utils;

enum CGLaravelControllerInitEnum
{
    case urlParams;
    case i18N;
    case moreParams;
    case request;
    case fingerprint;
    case theme;
    case styleConfig;
}
