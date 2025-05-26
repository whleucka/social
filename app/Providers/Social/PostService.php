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
                "is_bot" => $user->bot == 1,
                "uuid" => $post->uuid,
                "parent_uuid" => $post->getParent()?->uuid,
                "content" => html_entity_decode($post->content),
                "gravatar" => $user->gravatarUrl(),
                "name" => $user->first_name . ' ' . $user->surname,
                "username" => $user->username,
                "ago" => $post->ago(),
                "ping" => $this->shouldPing($post->created_at),
                "liked" => $post->isLiked($user_id),
                "like_count" => $post->likeCount(),
                "comment_count" => $post->commentCount(),
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

    public function createPost(int $user_id, string $content): Post|bool
    {
        $data = [
            "user_id" => $user_id,
            "ip" => ip2long(request()->getClientIp()),
            "content" => $content,
        ];
        $media = $this->extractMedia($content);
        if (isset($media["image"]) && $media["image"]) {
            $data["image"] = $media["image"];
        }
        if (isset($media['url']) && $media['url']) {
            $data["url"] = $media["url"];
        }
        return Post::create($data);
    }

    public function replyPost(int $user_id, string $content, string $uuid): Post|bool
    {
        $post = Post::where("uuid", $uuid)->get();

        if ($post) {
            $data = [
                "user_id" => $user_id,
                "ip" => ip2long(request()->getClientIp()),
                "parent_id" => $post->id,
                "content" => $content,
            ];
            return Post::create($data);
        }
        return false;
    }

    private function extractMedia(string $content)
    {
        $url = $this->extractLink($content);
        $image = $url ? $this->extractOgImage($url) : null;
        return [
            "url" => $url,
            "image" => $image,
        ];
    }

    private function extractOgImage($url): ?string
    {
        $html = @file_get_contents($url);
        if (!$html) return null;

        if (preg_match('/<meta property=["\']og:image["\'] content=["\']([^"\']+)["\']/', $html, $matches)) {
            return $matches[1];
        }
        return null;
    }

    private function extractLink(string $text): ?string 
    {
        preg_match_all(
            '/\bhttps?:\/\/[^\s<>()]+(?:\([\w\d]+\)|([^[:punct:]\s]|\/))/',
            $text,
            $matches
        );
        $url = $matches[0][0] ?? null;
        return filter_var($url, FILTER_VALIDATE_URL) ? $url : null;
    }

    public function getPosts(): ?array
    {
        return db()->fetchAll("SELECT uuid 
            FROM posts 
            WHERE created_at > NOW() - INTERVAL 30 DAY AND parent_id IS NULL
            ORDER BY created_at DESC");
    }

    public function getComments(string $uuid): ?array
    {
        $post = Post::where("uuid", $uuid)->get();

        if ($post) {
            return db()->fetchAll("SELECT uuid 
                FROM posts 
                WHERE parent_id = ?
                ORDER BY created_at DESC", [$post->id]);
        }
        return null;
    }

    public function searchPosts(string $term)
    {
        return db()->fetchAll("SELECT posts.uuid 
            FROM posts 
            INNER JOIN users ON users.id = user_id
            WHERE (username LIKE ? OR content LIKE ?)
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
