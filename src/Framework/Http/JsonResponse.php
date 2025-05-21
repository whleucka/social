<?php

namespace Echo\Framework\Http;

use Echo\Interface\Http\Response;

class JsonResponse implements Response
{
    private int $code;
    private array $headers;

    public function __construct(private array $content, ?int $code = null)
    {
        if (is_null($code)) {
            $this->code = http_response_code();
        } else {
            $this->code = $code;
        }
        $this->setHeader("Content-type", "application/json; charset=utf-8");
    }

    public function send(): void
    {
        ob_start();
        ob_clean();
        http_response_code($this->code);
        $this->sendHeaders();
        echo json_encode($this->content, JSON_PRETTY_PRINT);
    }

    public function setHeader(string $key, string $value): void
    {
        $this->headers[$key] = $value;
    }

    private function sendHeaders(): void
    {
        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }
    }
}
