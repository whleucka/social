<?php

namespace Echo\Framework\Http\Middleware;

use Closure;
use Echo\Framework\Http\JsonResponse;
use Echo\Framework\Http\Response as HttpResponse;
use Echo\Interface\Http\{Request, Middleware, Response};

/**
 * Request limit
 */
class RequestLimit implements Middleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $route = $request->getAttribute("route");
        $middleware = $route["middleware"];

        // Get the max requests and decay seconds
        if (in_array("api", $middleware)) {
            $max_requests = 60;
            $decay_seconds = 60;
        } else {
            $max_requests = $middleware["max_requests"] ?? config("security.max_requests");
            $decay_seconds = $middleware["decay_seconds"] ?? config("security.decay_seconds");
        }

        // Create a cache key
        $hash = md5($request->getClientIp());
        $key = "request_limit_" . $hash;

        // Set the session
        if (!session()->has($key)) {
            session()->set($key, [
                "count" => 0,
                "timestamp" => time(),
            ]);
        }

        $limit = session()->get($key);

        // Reset the count when expired
        if (time() - $limit["timestamp"] > $decay_seconds) {
            $limit["count"] = 0;
            $limit["timestamp"] = time();
        }

        // Increment request count
        $limit["count"]++;

        // Too many requests
        if ($limit["count"] > $max_requests) {
            $message = "Too many requests. Try again later.";
            return in_array("api", $middleware)
                ? new JsonResponse([
                    "id" => $request->getAttribute("request_id"),
                    "success" => 429,
                    "status" => false,
                    "data" => $message,
                    "ts" => date(DATE_ATOM)], 429)
                : new HttpResponse($message, 429);
        }

        // Set the request session
        session()->set($key, $limit);

        return $next($request);
    }
}
