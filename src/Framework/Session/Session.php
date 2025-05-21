<?php

namespace Echo\Framework\Session;

use Echo\Traits\Creational\Singleton;

ini_set('session.gc_maxlifetime', config("session.gc_maxlifetime"));
ini_set('session.gc_probability', config("session.gc_probability"));
ini_set('session.gc_divisor', config("session.gc_divisor"));
$session_path = config("paths.session");
if (!is_dir($session_path)) {
    mkdir($session_path, 01733, true);
    chown($session_path, 'www-data');
    chgrp($session_path, 'www-data');
}
session_save_path($session_path);

class Session
{
    use Singleton;

    private $data = [];

    public function __construct()
    {
        $this->data = $this->all();
    }

    /**
     * Set a session value
     */
    public function get(string $key): mixed
    {
        @session_start();
        session_write_close();
        $this->data = $_SESSION;
        return $this->data[$key] ?? null;
    }

    /**
     * Set a session key/value
     */
    public function set(string $key, mixed $value): void
    {
        @session_start();
        $this->data[$key] = $value;
        $_SESSION = $this->data;
        session_write_close();
    }

    /**
     * Delete a session key
     */
    public function delete(string $key): void
    {
        @session_start();
        unset($this->data[$key]);
        $_SESSION = $this->data;
        session_write_close();
    }

    /**
     * Checks existence of session key
     */
    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * Get all session variables
     */
    public function all(): array
    {
        @session_start();
        session_write_close();
        return $_SESSION;
    }

    /**
     * Destroy a session
     */
    public function destroy(): void
    {
        @session_start();
        $_SESSION = $this->data = [];
        session_destroy();
        session_write_close();
    }
}
