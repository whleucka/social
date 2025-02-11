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
        ];
    }

    public function csrf(): string
    {
        $twig = container()->get(\Twig\Environment::class);
        $token = session()->get("csrf_token");
        return $twig->render("components/csrf.html.twig", ["token" => $token]);
    }
}
