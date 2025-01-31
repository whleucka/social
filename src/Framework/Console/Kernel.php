<?php

namespace Echo\Framework\Console;

use Echo\Interface\Console\Kernel as ConsoleKernel;

class Kernel implements ConsoleKernel
{
    public function handle(string $command): void
    {
        echo 'wip: hi from console kernel!';
    }  
}
