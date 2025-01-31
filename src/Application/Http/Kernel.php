<?php

namespace Echo\Application\Http;

use Echo\Application\IKernel;

class Kernel implements IKernel
{
    public function run(): void
    {
        echo 'wip: hi from http kernel!';
    }  
}
