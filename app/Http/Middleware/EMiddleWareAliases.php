<?php

namespace App\Http\Middleware;

enum EMiddleWareAliases
{
    case auth;
    case auth_basic;
    case auth_session;
    case cache_headers;
    case can;
    case guest;
    case password_confirm;
    case precognitive;
    case signed;
    case throttle;
    case verified;
}
