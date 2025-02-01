<?php

namespace Echo\Framework\Http;

use Echo\Framework\Http\Request\Files;
use Echo\Framework\Http\Request\Get;
use Echo\Framework\Http\Request\Post;
use Echo\Interface\Http\Request as HttpRequest;

class Request implements HttpRequest
{
    public Get $get;
    public Post $post;
    public Files $files;
    private array $attributes = [];

    public function __construct(
        array $get = [],
        array $post = [],
        array $files = [],
    ) {
        $this->get = new Get($get);
        $this->post = new Post($post);
        $this->files = new Files($files);
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
