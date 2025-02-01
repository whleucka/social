<?php

namespace Echo\Interface\Routing;

interface Router
{
    public function dispatch(string $uri, string $method): ?array;
}
