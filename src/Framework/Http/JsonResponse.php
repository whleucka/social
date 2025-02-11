<?php

namespace Echo\Framework\Http;

use Echo\Interface\Http\Response;

class JsonResponse implements Response
{
    private int $code;

    public function __construct(private array $content, ?int $code = null)
    {
        if (is_null($code)) {
            $this->code = http_response_code();
        } else {
            $this->code = $code;
        }
    }

    public function send(): void
    {
        ob_start();
        ob_clean();
        http_response_code($this->code);
        echo json_encode($this->content, JSON_PRETTY_PRINT);
    }
}
