<?php

namespace Echo\Interface\Routing;

use Echo\Interface\Http\Request;

interface Router
{
    public function dispatch(Request $request): ?array;
}
