<?php

namespace Echo\Framework\Routing;

use Echo\Framework\Routing\Route;
use Error;
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
                if (is_subclass_of($instance, Route::class)) {
                    $http_method = strtolower((new ReflectionClass($instance))->getShortName());
                    if (!isset($this->routes[$instance->path][$http_method])) {
                        $this->routes[$instance->path][$http_method] = [
                            'controller' => $controller,
                            'method' => $method->getName(),
                            'middleware' => $instance->middleware,
                            'name' => $instance->name
                        ];
                    } else {
                        throw new Error("duplicate route detected: [$http_method] path: $instance->path");
                    }
                }
            }
        }
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }
}
