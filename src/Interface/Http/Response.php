<?php

namespace Echo\Interface\Http;

interface Response
{
    public function send(string $content, int $code = 200): void;
}
