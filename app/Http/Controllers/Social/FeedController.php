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

    #[Get("/", "feed.index")]
    public function index(): string
    {
        return $this->render("feed/index.html.twig");
    }

    #[Get("/feed/load", "feed.load")]
    public function load(): string
    {
        return $this->render("feed/load.html.twig", [
            "posts" => $this->post_provider->getPosts($this->user?->id),
        ]);
    }

    #[Get("/feed/page/{page}", "feed.more")]
    public function more(int $page): string
    {
        return $this->render("feed/more.html.twig", [
            "posts" => $this->post_provider->getPosts($this->user?->id, $page),
            "next_page" => $page + 1,
        ]);
    }
}
