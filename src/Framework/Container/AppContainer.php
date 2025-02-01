<?php

namespace Echo\Framework\Container;

use DI\Container;

class AppContainer
{
    private static ?Container $instance = null;

    public static function getInstance(): Container {
        if (self::$instance === null) {
            $definitions = config("container");
            $container = new Container($definitions);
            self::$instance = $container;
        }
        return self::$instance;
    }
}
