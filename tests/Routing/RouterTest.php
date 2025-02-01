<?php declare(strict_types=1);

namespace Tests\Routing;

use Echo\Framework\Http\Controller;
use Echo\Framework\Routing\Collector;
use Echo\Framework\Routing\Route\Get;
use Echo\Framework\Routing\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    private function dispatchRoute(string $uri, string $method)
    {
        $collector = new Collector;
        $collector->register(Routes::class);
        $router = new Router($collector);
        return $router->dispatch($uri, $method);
    }

    public function testRouterDispatchRoute()
    {
        $route = $this->dispatchRoute("/", "GET");

        // Test controller
        $this->assertSame("Tests\Routing\Routes", $route["controller"]);
        // Test method
        $this->assertSame("index", $route["method"]);
        // Test middleware
        $this->assertSame(["auth"], $route["middleware"]);
        // Test name
        $this->assertSame("routes.index", $route["name"]);

        // Testing method endpoints
        $route = $this->dispatchRoute("/numbers/1", "GET");
        $this->assertSame("numbers", $route["method"]);

        $route = $this->dispatchRoute("/numbers/9", "GET");
        $this->assertSame("numbers", $route["method"]);

        $route = $this->dispatchRoute("/numbers/10", "GET");
        $this->assertSame(null, $route);

        $route = $this->dispatchRoute("/id/420", "GET");
        $this->assertSame("id", $route["method"]);

        $route = $this->dispatchRoute("/id/testing", "GET");
        $this->assertSame("testing", $route["method"]);

        $route = $this->dispatchRoute("/slug/this-is-a-slug", "GET");
        $this->assertSame("slug", $route["method"]);

        $route = $this->dispatchRoute("/colour/blue", "GET");
        $this->assertSame("blue_red", $route["method"]);

        $route = $this->dispatchRoute("/colour/red", "GET");
        $this->assertSame("blue_red", $route["method"]);

        $route = $this->dispatchRoute("/colour/purple", "GET");
        $this->assertSame(null, $route);
    }
}

class Routes extends Controller
{
    #[Get("/", "routes.index", ["auth"])]    
    public function index()
    {
        return "index";
    }

    #[Get("/numbers/[0-9]", "routes.numbers")]    
    public function numbers()
    {
        return "numbers";
    }

    #[Get("/colour/(blue|red)", "routes.blue_red")]    
    public function blue_red()
    {
        return "blue_red";
    }

    #[Get("/slug/this-is-a-slug", "routes.slug")]    
    public function slug()
    {
        return 'slug';
    }

    #[Get("/id/{id}", "routes.id")]    
    public function id(int $id)
    {
        return $id;
    }

    #[Get("/id/testing", "routes.testing")]    
    public function testing()
    {
        return 'testing';
    }

}
