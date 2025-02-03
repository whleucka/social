<?php

namespace Echo\Framework\Http;

use Echo\Interface\Http\Response;

class JsonResponse implements Response
{
    public function __construct(private array $content)
    {
    }

    public function send(int $code = 200): void
    {
        ob_start();
        ob_clean();
        http_response_code($code);
        echo json_encode($this->content, JSON_PRETTY_PRINT);
    }
}
