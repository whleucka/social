<?php

namespace Echo\Framework\Http;

use Echo\Framework\Http\Response as HttpResponse;
use Echo\Framework\Routing\Collector;
use Echo\Framework\Routing\Router;
use Echo\Interface\Http\Kernel as HttpKernel;
use Echo\Interface\Http\Request;
use Echo\Interface\Http\Response;
use Error;
use Exception;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Kernel implements HttpKernel
{
    // Middleware layers
    protected array $middleware_layers = [];

    public function handle(Request $request): void
    {
        // Get web controllers
        $controller_path = config("paths.controllers");
        $controllers = $this->getControllers($controller_path);

        // Register application routes
        $collector = new Collector();
        foreach ($controllers as $controller) {
            $collector->register($controller);
        }

        // Dispatch the router
        $router = new Router($collector);
        $route = $router->dispatch($request->getUri(), $request->getMethod());

        // If there is no route, then 404
        if (is_null($route)) {
            http_response_code(404);
            exit;
        }

        // Set the current route in the request
        $request->setAttribute("route", $route);

        // Get controller payload
        $middleware = new Middleware();
        $response = $middleware->layer($this->middleware_layers)
            ->handle($request, fn () => $this->response($route, $request));

        $response->send();
        exit;
    }

    private function getControllers(string $directory): array
    {
        // Get existing classes before loading new ones
        $before = get_declared_classes();

        // Recursively find all PHP files
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
        foreach ($files as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                require_once $file->getPathname();
            }
        }

        // Get all declared classes after loading
        $after = get_declared_classes();

        // Return only the new classes
        return array_diff($after, $before);
    }

    private function response(array $route, Request $request): Response
    {
        // Resolve the controller
        $controller_class = $route['controller'];
        $method = $route['method'];
        $params = $route['params'];
        $middleware = $route['middleware'];
        $error = false;

        // Set the controller request
        try {
            // Using the container will allow for DI
            // in the controller constructor
            $controller = container()->get($controller_class);
            $controller->setRequest($request);

            // Set the content from the controller endpoint
            $content = $controller->$method(...$params);
        } catch (Exception $ex) {
            http_response_code(500);

            if (in_array("api", $middleware)) {
                $error = $ex->getMessage();
            } else {
                throw $ex;
            }
        } catch (Error $err) {
            http_response_code(400);

            if (in_array("api", $middleware)) {
                $error = $err->getMessage();
            } else {
                throw $err;
            }
        }

        // Create response (api or web)
        if (in_array("api", $middleware)) {
            $code = http_response_code();
            $api_response = [
                "id" => $request->getAttribute("request_id"),
                "success" => $code === 200,
                "status" => $code,
                "data" => $content ?? null,
                "ts" => date(DATE_ATOM),
            ];
            // Only show errors when debug is enabled
            if ($error && config("app.debug")) {
                $api_response["error"] = $error;
            }
            // API response
            return new JsonResponse($api_response);
        } else {
            // Web response
            return new HttpResponse($content);
        }
    }
}
