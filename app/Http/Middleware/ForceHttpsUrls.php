<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class ForceHttpsUrls
{
    /**
     * يجعل روابط asset() وغيرها https عندما يكون الطلب خلف بروكسي SSL.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->secure()) {
            URL::forceScheme('https');
        }

        return $next($request);
    }
}
