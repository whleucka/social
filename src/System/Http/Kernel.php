<?php

namespace Echo\System\Http;

use Echo\Interface\Http\Kernel as KernelInterface;

class Kernel implements KernelInterface
{
    public function run(): void
    {
        echo 'wip: hi from http kernel!';
    }  
}
