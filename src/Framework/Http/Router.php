<?php

namespace Echo\Framework\Http;

use Echo\Framework\Http\Route\Collector;
use Echo\Interface\Http\Request;
use Echo\Interface\Http\Router as HttpRouter;

class Router implements HttpRouter
{
    public function __construct(private Collector $collector)
    {
    }

    /**
     * Dispatch a new route
     */
    public function dispatch(Request $request): ?array
    {
        $uri = $request->getUri();
        $method = strtolower($request->getMethod());
        $routes = $this->collector->getRoutes();
        if (isset($routes[$uri][$method])) {
            return $routes[$uri][$method];
        }
        return null;
    }
}
