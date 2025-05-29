<?php

namespace App\Http\Controllers\Social;

use App\Providers\Social\PostService;
use App\Providers\Social\ProfileService;
use Echo\Framework\Http\Controller;
use Echo\Framework\Routing\Route\Get;

class ProfileController extends Controller
{
    public function __construct(
        private ProfileService $profile_provider, 
        private PostService $post_provider
    ) {}

    #[Get("/profile/{username}", "profile.index", ["auth"])]
    public function index(string $username): string
    {
        $user = $this->profile_provider->getUserByUsername($username);

        if ($user) {
            return $this->render("profile/index.html.twig", [
                "profile" => $user,
                "post_count" => $this->post_provider->getUserPostCount($user['id'])
            ]);
        }

        $this->pageNotFound();
    }

    #[Get("/profile/{username}/posts", "profile.posts", ["auth"])]
    public function posts(string $username): string
    {
        $user = $this->profile_provider->getUserByUsername($username);

        if ($user) {
            return $this->render("profile/load.html.twig", [
                "posts" => $this->post_provider->getUserPosts($user['id']),
                "profile" => $user,
                "section" => "posts",
            ]);
        }

        $this->pageNotFound();
    }

    #[Get("/profile/{username}/posts/page/{page}", "feed.more-posts", ["auth"])]
    public function more_posts(string $username, int $page): string
    {
        $user = $this->profile_provider->getUserByUsername($username);

        if ($user) {
            return $this->render("profile/more.html.twig", [
                "posts" => $this->post_provider->getUserPosts($user['id'], $page),
                "profile" => $user,
                "section" => "posts",
                "next_page" => $page + 1,
            ]);
        }

        $this->pageNotFound();
    }

    #[Get("/profile/{username}/replies", "profile.replies", ["auth"])]
    public function replies(string $username): string
    {
        $user = $this->profile_provider->getUserByUsername($username);

        if ($user) {
            return $this->render("profile/load.html.twig", [
                "posts" => $this->post_provider->getUserReplies($user['id']),
                "profile" => $user,
                "section" => "replies",
            ]);
        }

        $this->pageNotFound();
    }

    #[Get("/profile/{username}/replies/page/{page}", "feed.more-replies", ["auth"])]
    public function more_replies(string $username, int $page): string
    {
        $user = $this->profile_provider->getUserByUsername($username);

        if ($user) {
            return $this->render("profile/more.html.twig", [
                "posts" => $this->post_provider->getUserReplies($user['id'], $page),
                "profile" => $user,
                "section" => "replies",
                "next_page" => $page + 1,
            ]);
        }

        $this->pageNotFound();
    }

    #[Get("/profile/{username}/likes", "profile.likes", ["auth"])]
    public function likes(string $username): string
    {
        $user = $this->profile_provider->getUserByUsername($username);

        if ($user) {
            return $this->render("profile/load.html.twig", [
                "posts" => $this->post_provider->getUserLikes($user['id']),
                "profile" => $user,
                "section" => "likes",
            ]);
        }

        $this->pageNotFound();
    }

    #[Get("/profile/{username}/likes/page/{page}", "feed.more-likes", ["auth"])]
    public function more_likes(string $username, int $page): string
    {
        $user = $this->profile_provider->getUserByUsername($username);

        if ($user) {
            return $this->render("profile/more.html.twig", [
                "posts" => $this->post_provider->getUserLikes($user['id'], $page),
                "profile" => $user,
                "section" => "likes",
                "next_page" => $page + 1,
            ]);
        }

        $this->pageNotFound();
    }
}
