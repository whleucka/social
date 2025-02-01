<?php

namespace App\Http;

use Echo\Framework\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected array $middleware_layers = [
        \App\Http\Middleware\RequestID::class,
    ];
}
