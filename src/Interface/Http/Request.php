<?php

namespace Echo\Interface\Http;

interface Request
{
    public function getUri(): string;
    public function getMethod(): string;
    public function setAttribute(string $name, mixed $value): void;
    public function getAttribute(string $name): mixed;
    public function getAttributes(): array;
    public function getClientIp(): string;
    public function getHeaders(): array;
    public function getHeader(string $name): ?string;
}
