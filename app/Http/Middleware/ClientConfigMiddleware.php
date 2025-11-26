<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class ClientConfigMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $minutes = 60 * 24 * 30;

        if (!$request->hasCookie('theme')) {
            $response->headers->setCookie(cookie('theme', 'light', $minutes));
        } elseif ($request->has('theme')) {
            $theme = match ($request->get('theme')) {
                'light' => 'light',
                'dark' => 'dark',
                default => $request->cookie('theme', 'light'),
            };
            $response->headers->setCookie(cookie('theme', $theme, $minutes));
        }

        return $response;
    }
}
