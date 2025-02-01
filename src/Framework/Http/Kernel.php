<?php

namespace Echo\Framework\Http;

use Echo\Framework\Routing\Collector;
use Echo\Framework\Routing\Router;
use Echo\Interface\Http\Kernel as HttpKernel;
use Echo\Interface\Http\Request;
use Echo\Framework\Http\Response;

class Kernel implements HttpKernel
{
    // Middleware layers
    protected array $layers = [];

    public function handle(Request $request): void
    {
        // Get web controllers
        $controller_path = config("paths.controllers");
        $controllers = $this->getControllers($controller_path);

        // Register application routes
        $collector = container()->get(Collector::class);
        foreach ($controllers as $controller) {
            $collector->register($controller);
        }

        // Dispatch the router
        $router = container()->make(Router::class, ["collector" => $collector]);
        $route = $router->dispatch($request);

        // If there is no route, then 404
        if (!$route) {
            http_response_code(404);
            exit;
        }

        // Get controller payload
        $middleware = container()->get(Middleware::class);
        $content = $middleware->layer($this->layers)
            ->handle($request, fn () => $this->resolve($route, $request));

        // Send the response
        $response = container()->get(Response::class);
        $response->send($content);
    }

    private function getControllers(string $directory): array
    {
        // Get existing classes before loading new ones
        $before = get_declared_classes();

        foreach (glob($directory . '/*.php') as $file) {
            require_once $file;
        }
        // Get all declared classes after loading
        $after = get_declared_classes();

        // Return only the new classes
        return array_diff($after, $before);
    }

    private function resolve(array $route, Request $request)
    {
        // Resolve the controller endpoint
        $controller = container()->make($route['controller'], ['request' => $request]);
        $method = $route['method'];
        return $controller->$method();
    }
}
