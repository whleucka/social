<?php

namespace App;

use Echo\Framework\Http\Request;
use Echo\Interface\Console\Kernel as ConsoleKernel;
use Echo\Interface\Application as EchoApplication;
use Echo\Interface\Http\Kernel as HttpKernel;

class Application implements EchoApplication
{
    public function __construct(private ConsoleKernel|HttpKernel $kernel)
    {
    }

    public function run(): void
    {
        // Run the application (web or cli)
        if ($this->kernel instanceof HttpKernel) {
            $this->web();
        } elseif ($this->kernel instanceof ConsoleKernel) {
            $this->cli();
        }
    }

    private function web()
    {
        // Handle a web request
        $this->kernel->handle(new Request($_GET, $_POST, $_FILES));
    }

    private function cli()
    {
        // Run a command in cli mode
    }
}
