<?php

namespace Echo\Application\Console;

use Echo\Application\IKernel;

class Kernel implements IKernel
{
    public function run(): void
    {
        echo 'wip: hi from console kernel!';
    }  
}
