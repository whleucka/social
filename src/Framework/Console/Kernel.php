<?php

namespace Echo\Framework\Console;

use Echo\Interface\Console\Kernel as ConsoleKernel;

class Kernel implements ConsoleKernel
{
    protected array $commands = [];

    public function handle(): void
    {
        error_reporting(E_ERROR | E_WARNING | E_PARSE);
        $console = new \ConsoleKit\Console($this->commands);
        $console->run();
    }
}
