<?php

namespace App\Providers\Social;

use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;

class PostService
{
    public function createPost(int $user_id, string $comment): Post|bool
    {
        return Post::create([
            "user_id" => $user_id,
            "comment" => $comment,
        ]);
    }

    public function getPosts(int $user_id): ?array
    {
        $user = User::find($user_id);

        $posts = db()->fetchAll("SELECT * 
            FROM posts 
            WHERE user_id = ? 
            ORDER BY created_at DESC", [$user_id]);

        // What should we do about this?
        date_default_timezone_set('America/Edmonton');

        foreach ($posts as &$post) {
            $post['gravatar'] = $user->gravatarUrl();
            $post['name'] = $user->first_name . ' ' . $user->surname;
            $post['username'] = $user->username;
            $post['ago'] =  Carbon::parse($post['created_at'])->diffForHumans();
        }

        return $posts;
    }
}
