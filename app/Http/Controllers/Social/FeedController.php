<?php

namespace App\Http\Controllers\Social;

use App\Providers\Social\PostService;
use Echo\Framework\Http\Controller;
use Echo\Framework\Routing\Route\{Get, Post};

class FeedController extends Controller
{
    public function __construct(private PostService $post_provider)
    {
    }

    #[Get("/", "feed.index", ["auth"])]
    public function index(): string
    {
        return $this->render("feed/index.html.twig");
    }

    #[Get("/feed/load", "feed.load", ["auth"])]
    public function load(): string
    {
        return $this->render("feed/load.html.twig", [
            "posts" => $this->post_provider->getPosts($this->user->id),
        ]);
    }

    #[Post("/feed/search", "feed.search", ["auth"])]
    public function search()
    {
        $valid = $this->validate([
            "term" => ["required"],
        ]);

        if ($valid) {
            $this->setHeader("HX-Push-Url", "/");
            return $this->render("feed/load.html.twig", [
                "posts" => $this->post_provider->searchPosts($valid->term),
                "term" => $valid->term,
            ]);
        }

        return $this->load();
    }

}
