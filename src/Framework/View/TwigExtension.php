<?php

namespace Echo\Framework\View;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction("csrf", [$this, "csrf"]),
            new TwigFunction("uri", [$this, "uri"]),
        ];
    }

    public function csrf(): string
    {
        $twig = container()->get(\Twig\Environment::class);
        $token = session()->get("csrf_token");
        return $twig->render("components/csrf.html.twig", ["token" => $token]);
    }

    public function uri(string $name)
    {
        return uri($name);
    }
}
