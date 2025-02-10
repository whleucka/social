<?php

namespace Echo\Framework\Database;

use Echo\Interface\Database\Connection as DatabaseConnection;
use Echo\Interface\Database\Driver;
use Echo\Traits\Creational\Singleton;
use PDO;
use PDOException;
use RuntimeException;

final class Connection implements DatabaseConnection
{
    use Singleton;

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

    public function execute(string $sql, array $params = []): mixed
    {
        $this->connect();
        $stmt = $this->link->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetch(string $sql, array $params = []): array
    {
        return $this->execute($sql, $params)->fetch() ?: [];
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->execute($sql, $params)->fetchAll();
    }

    public function lastInsertId(): string
    {
        $this->connect();
        return $this->link->lastInsertId();
    }

    public function beginTransaction(): bool
    {
        $this->connect();
        return $this->link->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->link->commit();
    }

    public function rollback(): bool
    {
        return $this->link->rollBack();
    }

    public function getLink(): PDO
    {
        $this->connect();
        return $this->link;
    }
}
