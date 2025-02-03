<?php

namespace Echo\Framework\Container;

use Echo\Traits\Creational\Singleton;
use DI\Container as DIContainer;

class Container
{
    use Singleton;

    public static function getInstance(): DIContainer {
        if (self::$instance === null) {
            $definitions = config("container");
            $container = new DIContainer($definitions);
            self::$instance = $container;
        }
        return self::$instance;
    }
}
