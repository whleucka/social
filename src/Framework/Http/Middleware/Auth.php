<?php

namespace Echo\Framework\Http\Middleware;

use Closure;
use Echo\Interface\Http\{Request, Middleware, Response};

/**
 * Authentication (route)
 */
class Auth implements Middleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $route = $request->getAttribute("route");
        $middleware = $route["middleware"];
        $uuid = session()->get("user_uuid");

        if (in_array('auth', $middleware) && !$uuid) {
            redirect("/sign-in");
        }

        return $next($request);
    }
}
