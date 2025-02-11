<?php

namespace Echo\Framework\Http\Middleware;

use Closure;
use Echo\Framework\Http\Response as HttpResponse;
use Echo\Interface\Http\{Request, Middleware, Response};

/**
 * Whitelist
 */
class Whitelist implements Middleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $whitelist = config("security.whitelist");
        $ip = $request->getClientIp();

        if (!empty($whitelist) && !in_array($ip, $whitelist)) {
            return new HttpResponse("Access denied", 403);
        }

        return $next($request);
    }
}
