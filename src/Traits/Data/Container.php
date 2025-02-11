<?php

namespace Echo\Traits\Data;

trait Container
{
    public function __construct(private array $data)
    {
    }
    public function __get(string $name): mixed
    {
        return $this->data[$name] ?? null;
    }
    public function __set(string $name, mixed $value): void
    {
        $this->data[$name] = $value;
    }
    public function __isset(string $name): bool
    {
        return isset($this->data[$name]);
    }
    public function __unset(string $name): void
    {
        unset($this->data[$name]);
    }
    public function data(): array
    {
        return $this->data;
    }
}

