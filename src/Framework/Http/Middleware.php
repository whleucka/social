<?php

namespace Echo\Framework\Http;

use Closure;
use Echo\Interface\Http\Request;
use Echo\Interface\Http\Middleware as HttpMiddleware;

class Middleware
{
    public function __construct(private array $layers = [])
    {
    }

    public function layer($layers): Middleware
    {
        if ($layers instanceof Middleware) {
            $layers = $layers->toArray();
        }

        if ($layers instanceof HttpMiddleware) {
            $layers = [$layers];
        }

        if (!is_array($layers)) {
            throw new \InvalidArgumentException(
                get_class($layers) . " is not compatible middleware"
            );
        }

        return new static(array_merge($this->layers, $layers));
    }

    public function handle(Request $request, Closure $core): mixed
    {
        $coreFunction = $this->createCoreFunction($core);

        $layers = array_reverse($this->layers);

        $next = array_reduce(
            $layers,
            function ($nextLayer, $layer) {
                return $this->createLayer($nextLayer, $layer);
            },
            $coreFunction
        );

        return $next($request);
    }

    public function toArray(): array
    {
        return $this->layers;
    }

    private function createCoreFunction(Closure $core): Closure
    {
        return fn ($object) => $core($object);
    }

    private function createLayer($nextLayer, $layer): Closure
    {
        $layer = new $layer;
        return fn ($object) => $layer->handle($object, $nextLayer);
    }
}
