<?php

namespace Echo\Framework\Http\Route;

use Echo\Framework\Http\Route;
use ReflectionClass;

class Collector
{
    private array $routes = [];

    public function register(string $controllerClass): void
    {
        $reflection = new ReflectionClass($controllerClass);

        foreach ($reflection->getMethods() as $method) {
            foreach ($method->getAttributes() as $attribute) {
                $instance = $attribute->newInstance();

                if (is_subclass_of($instance, Route::class)) {
                    $httpMethod = strtolower((new ReflectionClass($instance))->getShortName());

                    $this->routes[$instance->path][$httpMethod] = [
                        'controller' => $controllerClass,
                        'method' => $method->getName(),
                        'middleware' => $instance->middleware,
                        'name' => $instance->name
                    ];
                }
            }
        }
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }
}
