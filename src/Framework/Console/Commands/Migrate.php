<?php

namespace Echo\Framework\Console\Commands;

/**
 * Database migration commands
 */
class Migrate extends \ConsoleKit\Command
{
    /**
     * Display current migration status
     */
    public function executeStatus(array $args, array $options = []): void
    {
        $this->writeln("wip: status");
    }

    /**
     * Run all migration files
     * If the database exists, then it will be dropped before creation (be careful)
     */
    public function executeFresh(array $args, array $options = []): void
    {
        $this->writeln("wip: fresh");
    }

    /**
     * Run up method on migration file
     */
    public function executeUp(array $args, array $options = []): void
    {
        $this->writeln("wip: up");
    }

    /**
     * Run down method on migration file
     */
    public function executeDown(array $args, array $options = []): void
    {
        $this->writeln("wip: down");
    }

    /**
     * Create a new database migration file
     */
    public function executeCreate(array $args, array $options = []): void
    {
        $this->writeln("wip: create");
    }
}

