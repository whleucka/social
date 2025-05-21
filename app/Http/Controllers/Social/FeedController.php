<?php

namespace App\Http\Controllers\Social;

use App\Providers\Social\PostService;
use Echo\Framework\Http\Controller;
use Echo\Framework\Routing\Route\Get;

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
}
