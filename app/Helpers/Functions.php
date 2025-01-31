<?php

use Echo\System\Http\Kernel as HttpKernel;
use Echo\System\Console\Kernel as ConsoleKernel;

function app()
{
    return new HttpKernel;
}

function console()
{
    return new ConsoleKernel;
}
