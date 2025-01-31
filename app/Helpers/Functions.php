<?php

use Echo\Application\Http\Kernel as HttpKernel;
use Echo\Application\Console\Kernel as ConsoleKernel;

function app()
{
    return new HttpKernel;
}

function console()
{
    return new ConsoleKernel;
}
