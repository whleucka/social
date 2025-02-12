<?php

namespace Echo\Framework\Console\Commands;


/**
 * Display the current application version
 */
class Version extends \ConsoleKit\Command
{
    public function execute(array $args, array $options = []): void
    {
        $this->writeln(config("app.version"), \ConsoleKit\Colors::GREEN);
    }
}
