<?php

namespace App\Providers\Social;

use DateTime;
use App\Models\Post;
use App\Models\PostLike;

class PostService
{
    public function getPost(int $user_id, string $uuid)
    {
        $post = Post::where("uuid", $uuid)->get();
        if ($post) {
            $user = $post->user();
            return [
                "uuid" => $post->uuid,
                "comment" => $post->comment,
                "gravatar" => $user->gravatarUrl(),
                "name" => $user->first_name . ' ' . $user->surname,
                "username" => $user->username,
                "ago" => $post->ago(),
                "ping" => $this->shouldPing($post->created_at),
                "liked" => $post->isLiked($user_id),
                "like_count" => $post->likeCount(),
                "image" => $post->image,
                "url" => $post->url,
            ];
        }
        return null;
    }

    private function shouldPing(string $datetime)
    {
        $input_time = new DateTime($datetime);
        $now = new DateTime();
        $diff = abs($now->getTimestamp() - $input_time->getTimestamp());
        return $diff <= 60;
    }

    public function getPostAgo(string $uuid)
    {
        $post = Post::where("uuid", $uuid)->get();
        if ($post) {
            return $post->ago();
        }
        return null;
    }

    public function createPost(int $user_id, string $comment): Post|bool
    {
        return Post::create([
            "user_id" => $user_id,
            "comment" => $comment,
        ]);
    }

    public function getPosts(): ?array
    {
        return db()->fetchAll("SELECT uuid 
            FROM posts 
            WHERE created_at > NOW() - INTERVAL 30 DAY
            ORDER BY created_at DESC");
    }

    public function searchPosts(string $term)
    {
        return db()->fetchAll("SELECT posts.uuid 
            FROM posts 
            INNER JOIN users ON users.id = user_id
            WHERE (username LIKE ? OR comment LIKE ?)
            ORDER BY posts.created_at DESC", ["%$term%", "%$term%"]);
    }

    public function isLiked(int $user_id, string $uuid)
    {
        $post = Post::where("uuid", $uuid)->get();

        if ($post) {
            return $post->isLiked($user_id);
        }
        return null;
    }

    public function getLikeCount(string $uuid)
    {
        $post = Post::where("uuid", $uuid)->get();

        if ($post) {
            return $post->likeCount();
        }
        return null;
    }

    public function likePost(int $user_id, string $uuid)
    {
        $post = Post::where("uuid", $uuid)->get();

        if ($post) {
            if ($post->isLiked($user_id)) {
                $like = PostLike::where("user_id", $user_id)
                    ->andWhere("post_id", $post->id)->get();
                $like->delete();
            } else {
                PostLike::create([
                    "user_id" => $user_id,
                    "post_id" => $post->id,
                ]);
            }
            return true;
        }
        return null;
    }
}
