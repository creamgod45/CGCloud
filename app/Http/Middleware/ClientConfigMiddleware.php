<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class ClientConfigMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->hasCookie('theme')) {
            // 呼叫下一個請求
            $response = $next($request);
            // 設定 cookie 的有效期限（例如 30 天）
            $minutes = 60 * 24 * 30;
            // 將 cookie 加入回應中
            $cookie = Cookie::make('theme', 'light', $minutes);
            return $response->cookie($cookie);
        } elseif ($request->has('theme')) {
            // 呼叫下一個請求
            $response = $next($request);
            $theme = match ($request->get('theme')) {
                'light' => 'light',
                'dark' => 'dark',
                default => Cookie::get('theme'),
            };
            // 設定 cookie 的有效期限（例如 30 天）
            $minutes = 60 * 24 * 30;
            // 將 cookie 加入回應中
            $cookie = Cookie::make('theme', $theme, $minutes);
            return $response->cookie($cookie);
        }
        return $next($request);
    }
}
