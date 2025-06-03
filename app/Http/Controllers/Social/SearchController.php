<?php

namespace App\Http\Controllers\Social;

use App\Providers\Social\PostService;
use Echo\Framework\Http\Controller;
use Echo\Framework\Routing\Route\{Get, Post};

class SearchController extends Controller
{
    public function __construct(private PostService $post_provider)
    {
    }

    #[Get("/search", "search.index")]
    public function index()
    {
        $this->setHeader("HX-Push-Url", "/search");

        return $this->render("search/index.html.twig", []);
    }

    #[Post("/search", "search.query")]
    public function query()
    {
        $valid = $this->validate([
            "term" => ["required"],
        ]);

        if ($valid) {
            return $this->render("feed/load.html.twig", [
                "posts" => $this->post_provider->searchPosts($valid->term),
                "term" => $valid->term,
            ]);
        }

        return $this->index();
    }
}
