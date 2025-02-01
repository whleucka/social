<?php

namespace Echo\Framework\Http;

use Echo\Framework\Http\Request\{Get, Post, Files, Cookie};
use Echo\Interface\Http\Request as HttpRequest;

class Request implements HttpRequest
{
    public Get $get;
    public Post $post;
    public Files $files;
    public Cookie $cookie;

    private array $attributes = [];

    public function __construct(
        array $get = [],
        array $post = [],
        array $files = [],
        array $cookie = [],
    ) {
        $this->get = new Get($get);
        $this->post = new Post($post);
        $this->files = new Files($files);
        $this->cookie = new Cookie($cookie);
    }

    public function getUri(): string
    {
        return strtok($_SERVER["REQUEST_URI"], '?');
    }

    public function getMethod(): string
    {
        return $_SERVER["REQUEST_METHOD"];
    }

    public function getAttribute(string $name): mixed
    {
        return $this->attributes[$name];
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function setAttribute(string $name, mixed $value): void
    {
        $this->attributes[$name] = $value;
    }
}
