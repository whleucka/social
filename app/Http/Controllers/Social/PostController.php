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

    #[Get("/post/{uuid}")]
    public function index(string $uuid)
    {
        $this->setHeader("HX-Push-Url", "/post/$uuid");
        return $this->render("post/index.html.twig", [
            "post" => $this->post_provider->getPost($this->user->id, $uuid)
        ]);
    }

    #[Get("/post/{uuid}/load", "post.load", ["auth"])]
    public function load(string $uuid): string
    {
        return $this->render("post/load.html.twig", [
            "post" => $this->post_provider->getPost($this->user->id, $uuid)
        ]);
    }

    #[Get("/post/{uuid}/comments/load", "post.comments", ["auth"])]
    public function comments(string $uuid)
    {
        return $this->render("post/load-comments.html.twig", [
            "posts" => $this->post_provider->getComments($uuid),
            "uuid" => $uuid,
        ]);
    }

    #[Get("/post/{uuid}/comments/page/{page}", "post.more-comments", ["auth"])]
    public function more_comments(string $uuid, int $page)
    {
        return $this->render("post/more-comments.html.twig", [
            "posts" => $this->post_provider->getComments($uuid, $page),
            "uuid" => $uuid,
            "next_page" => $page + 1,
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

    #[Get("/post/control/{uuid}", "post.control-reply", ["auth"])]
    public function control_reply(string $uuid): string
    {
        return $this->render("post/control.html.twig", [
            "post" => $this->post_provider->getPost($this->user->id, $uuid),
        ]);
    }

    #[Post("/post/create", "post.create", ["auth"])]
    public function create(): ?string
    {
        $valid = $this->validate([
            "content" => ["required"],
        ]);

        if ($valid) {
            $post = $this->post_provider->createPost($this->user->id, $valid->content);
            if ($post) {
                if ($_SERVER['HTTP_REFERER'] === $_SERVER['HTTP_ORIGIN'].'/') {
                    return $this->render("post/post.html.twig", [
                        "post" => ["uuid" => $post->uuid],
                    ]);
                }
                header("HX-Redirect: /");
                exit;
            }
        }
        return null;
    }

    #[Post("/post/{uuid}/reply", "post.reply", ["auth"])]
    public function reply(string $uuid)
    {
        $valid = $this->validate([
            "content" => ["required"],
        ]);

        if ($valid) {
            $post = $this->post_provider->replyPost($this->user->id, $valid->content, $uuid);
            if ($post) {
                if ($_SERVER['HTTP_REFERER'] === $_SERVER['HTTP_ORIGIN']."/post/$uuid") {
                    // Reply goes in the comments
                    $this->setHeader("HX-Retarget", "#comments");
                    return $this->render("post/post.html.twig", [
                        "post" => ["uuid" => $post->uuid],
                    ]);
                }
                header("HX-Redirect: /post/$uuid");
                exit;
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
