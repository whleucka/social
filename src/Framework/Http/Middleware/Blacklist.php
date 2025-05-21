<?php

namespace Echo\Framework\Http\Middleware;

use Closure;
use Echo\Framework\Http\Response as HttpResponse;
use Echo\Interface\Http\{Request, Middleware, Response};

/**
 * Blacklist
 */
class Blacklist implements Middleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $blacklist = config("security.blacklist");
        $ip = $request->getClientIp();

        if (in_array($ip, $blacklist)) {
            return new HttpResponse("Access denied", 403);
        }

        return $next($request);
    }
}
