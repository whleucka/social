<?php

namespace Echo\Framework\Routing;

use Echo\Framework\Routing\Collector;
use Echo\Interface\Routing\Router as RouterInterface;

class Router implements RouterInterface
{
    public function __construct(private Collector $collector)
    {
    }

    /**
     * Dispatch a new route
     */
    public function dispatch(string $uri, string $method): ?array
    {
        $method = strtolower($method);
        $routes = $this->collector->getRoutes();

        // Check for an exact match first
        if (isset($routes[$uri][$method])) {
            // There are no params
            $routes[$uri][$method]['params'] = [];
            return $routes[$uri][$method];
        }

        // Check for parameterized routes
        foreach ($routes as $route => $methods) {
            $pattern = preg_replace('/\{(\w+)\}/', '([A-Za-z0-9-]+)', $route);
            if (preg_match("#^$pattern$#", $uri, $matches)) {
                // Ensure the requested method is valid for this route
                if (!isset($methods[$method])) {
                    return null;
                }

                // Remove the full match
                array_shift($matches);
                // Add available params
                $methods[$method]['params'] = $matches;
                return $methods[$method];
            }
        }

        return null;
    }
}
