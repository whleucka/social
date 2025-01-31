<?php

namespace App\Http\Middlewares;

use Echo\Interface\Http\{Request, Middlewares};
use Closure;

/**
 * Adds an ID to request
 */
class RequestID implements Middlewares
{
    public function handle(Request $request, Closure $next): mixed
    {
        $request->setAttribute('id', uniqid(more_entropy: true));

        $response = $next($request);

        return $response;
    }
}
