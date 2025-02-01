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

        // Check for an exact match first
        if (isset($routes[$uri][$method])) {
            return ['handler' => $routes[$uri][$method], 'params' => []];
        }

        // Check for parameterized routes
        foreach ($routes as $route => $methods) {
            $pattern = preg_replace('/\{(\w+)\}/', '([^/]+)', $route);
            if (preg_match("#^$pattern$#", $uri, $matches)) {
                array_shift($matches); // Remove full match
                return ['handler' => $methods[$method] ?? null, 'params' => $matches];
            }
        }

        return null;
    }
}
