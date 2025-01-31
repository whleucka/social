<?php

use App\Application;
use App\Http\Kernel as HttpKernel;

function dump(mixed $payload)
{
    printf("<pre>%s</pre>", print_r($payload, true));
}

function dd(mixed $payload)
{
    // Dump and die
    dump($payload);
    die;
}

function app()
{
    $kernel = new HttpKernel();
    return new Application($kernel);
}

function console()
{
    dd("wip");
}

function config(string $name)
{
    $name_split = explode(".", $name);
    $config_target = __DIR__ . "/../Config/" . $name_split[0] . ".php";

    if (is_file($config_target)) {
        $config = require $config_target;

        // Traverse nested keys dynamically
        $value = $config;
        for ($i = 1; $i < count($name_split); $i++) {
            if (!isset($value[$name_split[$i]])) {
                return null; // Return null if key doesn't exist
            }
            $value = $value[$name_split[$i]];
        }

        return $value;
    }

    return null; // Return null if file doesn't exist
}
