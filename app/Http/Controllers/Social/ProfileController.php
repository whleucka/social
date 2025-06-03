<?php

namespace App\Http\Controllers\Social;

use App\Providers\Social\PostService;
use App\Providers\Social\ProfileService;
use Echo\Framework\Http\Controller;
use Echo\Framework\Routing\Route\{ Get, Post };

class ProfileController extends Controller
{
    private $profile = null;

    public function __construct(
        private ProfileService $profile_provider, 
        private PostService $post_provider
    ) {
        // username required for all routes
        $this->setProfile();
    }

    private function setProfile()
    {
        $params = request()->getAttribute("route")["params"];
        if ($params && isset($params[0])) {
            $this->profile = $this->profile_provider->getUserByUsername($params[0]);
        }
        if (!$this->profile) {
            $this->pageNotFound();
        }
    }

    #[Get("/profile/{username}", "profile.index")]
    public function index(string $username): string
    {
        $this->setHeader("HX-Push-Url", "/profile/$username");
        return $this->render("profile/index.html.twig", [
            "profile" => $this->profile,
            "post_count" => $this->post_provider->getUserPostCount($this->profile['id']),
            "has_edit" => $this->profile['id'] === $this->user->id,
        ]);
    }

    #[Get("/profile/{username}/edit", "profile.edit", ["auth"])]
    public function edit(string $username)
    {
        if ($this->profile['id'] === $this->user->id) {
            return $this->render("profile/edit.html.twig", [
                "profile" => [
                    "username" => $username,
                    "first_name" => $this->user->first_name,
                    "surname" => $this->user->surname,
                    "description" => $this->user->description,
                ],
            ]);
        }
    }

    #[Post("/profile/{username}/save", "profile.save", ["auth"])]
    public function save(string $username): string
    {
        if ($this->profile['id'] === $this->user->id) {
            $valid = $this->validate([
                "first_name" => ["required", "max_length:20"],
                "surname" => ["required", "max_length:20"],
                "description" => ["max_length:255"],
            ]);

            if ($valid) {
                $this->profile_provider->save($username, $valid->first_name, $valid->surname, $valid->description);
                $this->setProfile();
            }
        }
        return $this->index($username);
    }

    #[Get("/profile/{username}/posts/load", "profile.posts")]
    public function posts(string $username)
    {
        return $this->render("profile/load.html.twig", [
            "posts" => $this->post_provider->getUserPosts($this->profile['id']),
            "profile" => $this->profile,
            "section" => "posts",
        ]);
    }

    #[Get("/profile/{username}/posts/page/{page}", "feed.more-posts")]
    public function more_posts(string $username, int $page)
    {
        return $this->render("profile/more.html.twig", [
            "posts" => $this->post_provider->getUserPosts($this->profile['id'], $page),
            "profile" => $this->profile,
            "section" => "posts",
            "next_page" => $page + 1,
        ]);
    }

    #[Get("/profile/{username}/replies/load", "profile.replies")]
    public function replies(string $username)
    {
        return $this->render("profile/load.html.twig", [
            "posts" => $this->post_provider->getUserReplies($this->profile['id']),
            "profile" => $this->profile,
            "section" => "replies",
        ]);
    }

    #[Get("/profile/{username}/replies/page/{page}", "feed.more-replies")]
    public function more_replies(string $username, int $page)
    {
        return $this->render("profile/more.html.twig", [
            "posts" => $this->post_provider->getUserReplies($this->profile['id'], $page),
            "profile" => $this->profile,
            "section" => "replies",
            "next_page" => $page + 1,
        ]);
    }

    #[Get("/profile/{username}/likes/load", "profile.likes")]
    public function likes(string $username)
    {
        return $this->render("profile/load.html.twig", [
            "posts" => $this->post_provider->getUserLikes($this->profile['id']),
            "profile" => $this->profile,
            "section" => "likes",
        ]);
    }

    #[Get("/profile/{username}/likes/page/{page}", "feed.more-likes")]
    public function more_likes(string $username, int $page)
    {
        return $this->render("profile/more.html.twig", [
            "posts" => $this->post_provider->getUserLikes($this->profile['id'], $page),
            "profile" => $this->profile,
            "section" => "likes",
            "next_page" => $page + 1,
        ]);
    }
}
