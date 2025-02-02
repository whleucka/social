<?php

use App\Application;
use App\Http\Kernel as HttpKernel;
use App\Console\Kernel as ConsoleKernel;
use Echo\Framework\Container\Container;
use Echo\Framework\Database\Connection;
use Echo\Framework\Database\Drivers\MySQL;

/**
 * Get application container
 */
function container()
{
    return Container::getInstance();
}

/**
 * Get PDO DB
 */
function db()
{
    $mysql = container()->get(MySQL::class);
    return Connection::getInstance($mysql);
}

function env(string $name, mixed $default = null)
{
    if (!isset($_ENV[$name])) {
        $_ENV[$name] = $default;
    }
    return $_ENV[$name];
}

/**
 * Dump
 */
function dump(mixed $payload): void
{
    printf("<pre>%s</pre>", print_r($payload, true));
}

/**
 * Dump & die
 */
function dd(mixed $payload): void
{
    dump($payload);
    die;
}

/**
 * Web application
 */
function app(): Application
{
    $kernel = new HttpKernel;
    return new Application($kernel);
}

/**
 * Console application
 */
function console(): Application
{
    $kernel = new ConsoleKernel;
    return new Application($kernel);
}

/**
 * Get application config
 */
function config(string $name)
{
    $name_split = explode(".", $name);
    $config_target = __DIR__ . "/../Config/" . strtolower($name_split[0]) . ".php";

    if (is_file($config_target)) {
        $config = require $config_target;

        // Traverse nested keys dynamically
        $value = $config;
        for ($i = 1; $i < count($name_split); $i++) {
            if (!isset($value[$name_split[$i]])) {
                return null; // Doesn't exist
            }
            $value = $value[$name_split[$i]];
        }

        return $value;
    }

    return null; // Doesn't exist
}
