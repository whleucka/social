<?php

namespace Echo\Framework\Container;

use DI\Container;
use Echo\Traits\Creational\Singleton;

class AppContainer
{
    use Singleton;

    public static function getInstance(): Container {
        if (self::$instance === null) {
            $definitions = config("container");
            $container = new Container($definitions);
            self::$instance = $container;
        }
        return self::$instance;
    }
}
