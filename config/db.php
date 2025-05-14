<?php

return [
    "name" => env("DB_NAME"),
    "username" => env("DB_USERNAME"),
    "password" => env("DB_PASSWORD"),
    "host" => env("DB_HOST"),
    "port" => env("DB_PORT"),
    "charset" => env("DB_CHARSET"),
    "options" => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_PERSISTENT         => false,
    ]
];
