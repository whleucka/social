<?php

namespace Echo\Interface\Routing;

interface Router
{
    public function dispatch(string $uri, string $method): ?array;
    public function searchUri(string $name, ...$params): ?string;
}
