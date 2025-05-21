<?php

namespace Echo\Framework\Routing;

use Echo\Framework\Routing\Route;
use Exception;
use ReflectionClass;

class Collector
{
    private array $routes = [];

    public function register(string $controller): void
    {
        $reflection = new ReflectionClass($controller);

        foreach ($reflection->getMethods() as $method) {
            foreach ($method->getAttributes() as $attribute) {
                $instance = $attribute->newInstance();
                if (!is_subclass_of($instance, Route::class)) {
                    continue;
                }

                $http_method = strtolower((new ReflectionClass($instance))->getShortName());

                // Check for duplicate route name
                foreach ($this->routes as $routesByMethod) {
                    foreach ($routesByMethod as $route) {
                        if ($route['name'] === $instance->name) {
                            throw new Exception("Duplicate route name detected: '{$instance->name}'");
                        }
                    }
                }

                // Check for duplicate path & HTTP method
                if (isset($this->routes[$instance->path][$http_method])) {
                    throw new Exception("Duplicate route detected: [$http_method] path: {$instance->path}");
                }

                // Register the route
                $this->routes[$instance->path][$http_method] = [
                    'controller' => $controller,
                    'method' => $method->getName(),
                    'middleware' => $instance->middleware,
                    'name' => $instance->name
                ];
            }
        }
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }
}
