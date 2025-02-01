<?php

namespace Echo\Interface\Http;

use Echo\Interface\Http\Request;
use Closure;

interface Middleware
{
    public function handle(Request $request, Closure $next): mixed;
}
