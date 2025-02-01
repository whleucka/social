<?php

namespace Echo\Framework\Routing;

use Echo\Framework\Routing\Collector;
use Echo\Interface\Http\Request;
use Echo\Interface\Routing\Router as RouterInterface;

class Router implements RouterInterface
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
