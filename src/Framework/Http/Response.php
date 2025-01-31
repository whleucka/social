<?php

namespace Echo\Framework\Http;

use Echo\Interface\Http\Response as InterfaceResponse;

class Response implements InterfaceResponse
{
    public function send(string $content, int $code = 200): void
    {
        ob_start();
        ob_clean();
        http_response_code($code);
        echo $content;
    }
}
