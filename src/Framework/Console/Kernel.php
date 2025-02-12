<?php

namespace Echo\Framework\Console;

use Echo\Interface\Console\Kernel as ConsoleKernel;

class Kernel implements ConsoleKernel
{
    protected array $commands = [];

    public function handle(): void
    {
        $console = new \ConsoleKit\Console($this->commands);
        $console->run();
    }
}
