<?php

namespace Echo\Framework\Http\Middleware;

use Closure;
use Echo\Interface\Http\{Request, Middleware, Response};

/**
 * Adds an ID to request
 */
class RequestID implements Middleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->setAttribute('request_id', uniqid(more_entropy: true));

        return $next($request);
    }
}
