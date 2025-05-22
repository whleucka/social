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

    #[Get("/post/{uuid}/load", "post.load", ["auth"])]
    public function load(string $uuid): string
    {
        return $this->render("post/load.html.twig", [
            "post" => $this->post_provider->getPost($uuid)
        ]);
    }

    #[Get("/post/{uuid}/ping", "post.ping", ["auth"])]
    public function ping(string $uuid)
    {
        return $this->post_provider->getPostAgo($uuid);
    }

    #[Get("/post/control", "post.control", ["auth"])]
    public function control(): string
    {
        return $this->render("post/control.html.twig", []);
    }

    #[Post("/post/comment", "post.comment", ["auth"])]
    public function comment(): ?string
    {
        $valid = $this->validate([
            "comment" => ["required"],
        ]);

        if ($valid) {
            $post = $this->post_provider->createPost($this->user->id, $valid->comment);
            if ($post) {
                return $this->render("post/index.html.twig", [
                    "post" => ["uuid" => $post->uuid],
                ]);
            }
        }
        return null;
    }

    #[Get("/post/{uuid}/like", "post.like", ["auth"])]
    public function like(string $uuid): string
    {
        $this->post_provider->likePost($this->user->id, $uuid);
        return $this->render("post/like.html.twig", [
            "liked" => $this->post_provider->isLiked($this->user->id, $uuid),
            "count" => $this->post_provider->getLikeCount($uuid),
            "uuid" => $uuid,
        ]);
    }
}
