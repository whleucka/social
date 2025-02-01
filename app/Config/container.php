<?php

use Echo\Framework\Http\Request;

return [
    Request::class => DI\create()->constructor($_GET, $_POST, $_FILES),
];
