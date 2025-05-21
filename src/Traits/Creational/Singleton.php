<?php

namespace Echo\Traits\Creational;

trait Singleton {
    private static $instance = null;

    public function __construct() {}
    public function __clone() {}
    public function __wakeup() {}

    public static function getInstance(): static {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        return static::$instance;
    }
}
