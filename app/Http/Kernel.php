<?php

namespace App\Http;

use Echo\Framework\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected array $layers = [
        \App\Http\Middlewares\RequestID::class,
    ];
}
