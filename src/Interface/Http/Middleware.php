<?php

namespace Echo\Interface\Http;

use Echo\Interface\Http\Request;
use Closure;

interface Middleware
{
    public function layer($layers): Middleware;
    public function handle(Request $request, Closure $core): mixed;
    public function toArray(): array;
}
