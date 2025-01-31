<?php

namespace Echo\System\Console;

use Echo\Interface\Console\Kernel as KernelInterface;

class Kernel implements KernelInterface
{
    public function run(): void
    {
        echo 'wip: hi from console kernel!';
    }  
}
