<?php

use Echo\Framework\Database\Drivers\MySQL;
use Echo\Framework\Http\Request;

return [
    Request::class => DI\create()->constructor($_GET, $_POST, $_FILES, $_COOKIE),
    MySQL::class => DI\create()->constructor(
        config("db.name"),
        config("db.username"),
        config("db.password"),
        config("db.host"),
        (int) config("db.port"),
        config("db.charset"),
        config("db.options"),
    ),
];
