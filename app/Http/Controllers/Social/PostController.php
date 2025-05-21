<?php

namespace App\Http\Controllers\Social;

use App\Providers\Social\PostService;
use Echo\Framework\Http\Controller;
use Echo\Framework\Routing\Route\{Get, Post};

class PostController extends Controller
{
    public function __construct(private PostService $post_provider)
    {
    }

    #[Get("/post/control", "post.control", ["auth"])]
    public function control(): string
    {
        return $this->render("post/control.html.twig", []);
    }

    #[Post("/post/comment", "post.comment", ["auth"])]
    public function comment()
    {
        $valid = $this->validate([
            "comment" => ["required"],
        ]);

        if ($valid) {
            $this->post_provider->createPost($this->user->id, $valid->comment);
            $this->hxTrigger("load-posts");
        }
    }
}
