<?php

namespace Echo\Framework\Database;

use Echo\Interface\Database\Driver;
use PDO;
use PDOException;
use RuntimeException;

class Connection
{
    private static ?Connection $instance = null;
    private ?PDO $link = null;
    private Driver $driver;

    private function __construct(Driver $driver)
    {
        $this->driver = $driver;
        $this->connect();
    }

    public static function getInstance(Driver $driver): Connection
    {
        if (self::$instance === null) {
            self::$instance = new self($driver);
        }
        return self::$instance;
    }

    private function connect(): void
    {
        if ($this->link === null) {
            try {
                $this->link = new PDO(
                    $this->driver->getDsn(),
                    $this->driver->getUsername(),
                    $this->driver->getPassword(),
                    $this->driver->getOptions()
                );
            } catch (PDOException $e) {
                throw new RuntimeException('Database connection failed: ' . $e->getMessage());
            }
        }
    }

    public function getLink(): PDO
    {
        $this->connect();
        return $this->link;
    }
}
