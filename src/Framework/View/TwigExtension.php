<?php

namespace Echo\Framework\View;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction("csrf", [$this, "csrf"]),
            new TwigFunction("uri", [$this, "uri"]),
            new TwigFunction("old", [$this, "old"]),
        ];
    }

    public function getFilters()
    {
        return [
            new TwigFilter("linkify", [$this, "linkify"]),
        ];
    }

    public function csrf(): string
    {
        $token = session()->get("csrf_token");
        return twig()->render("components/csrf.html.twig", ["token" => $token]);
    }

    public function uri(string $name)
    {
        return uri($name);
    }

    public function old(string $name, mixed $default = null)
    {
        return request()->request->get($name) ?? $default;
    }

    function linkify(string $text): string {
        // Escape first to prevent XSS
        $escaped = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

        // Convert URLs to <a> links
        $linked = preg_replace_callback(
            '/(https?:\/\/[^\s<]+)/i',
            function ($matches) {
                $url = $matches[1];
                return "<a href=\"$url\" target=\"_blank\" rel=\"noopener noreferrer\">$url</a>";
            },
            $escaped
        );

        // Convert newlines to <br>
        return nl2br($linked, false); // false: don't use XHTML-style <br />
    }
}
