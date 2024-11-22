<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IframeOptionsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // 設置 X-Frame-Options 標頭為 SAMEORIGIN
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        return $response;
    }
}
