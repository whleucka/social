<?php

namespace App\Providers\Social;

use DateTime;
use App\Models\Post;
use App\Models\PostLike;
use DOMDocument;

class PostService
{
    public function getPostByUUID(string $uuid)
    {
        return Post::where("uuid", $uuid)->get();
    }

    public function getPost(int $user_id, string $uuid)
    {
        $post = $this->getPostByUUID($uuid);
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
        $post = $this->getPostByUUID($uuid);
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
        $post = $this->getPostByUUID($uuid);
        if ($post) {
            $data = [
                "user_id" => $user_id,
                "ip" => ip2long(request()->getClientIp()),
                "parent_id" => $post->id,
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
        return false;
    }

    private function extractMedia(string $content)
    {
        $url = $this->extractLink($content);
        $image = $url ? $this->extractMetaImage($url) : null;
        return [
            "url" => $url,
            "image" => $image,
        ];
    }

    private function fetchUrl(string $url): ?string
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => config("app.debug"),
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            CURLOPT_TIMEOUT => 10,
        ]);
        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('cURL error: ' . curl_error($ch));
        }
        curl_close($ch);
        return $response ?: null;
    }

    private function extractMetaImage(string $url): ?string
    {
        $html = $this->fetchUrl($url);
        if (!$html) return null;

        $doc = new DOMDocument();

        @$doc->loadHTML($html);

        $metaTags = $doc->getElementsByTagName('meta');
        $candidates = [
            'og:image',
            'og:image:secure_url',
            'twitter:image',
            'twitter:image:src',
            'image', // generic fallback
            'thumbnail',
            'logo',
            'apple-touch-icon',
        ];

        foreach ($metaTags as $tag) {
            $property = $tag->getAttribute('property');
            $name = $tag->getAttribute('name');
            $itemprop = $tag->getAttribute('itemprop');
            $content = $tag->getAttribute('content');

            if (in_array($property, $candidates) ||
                in_array($name, $candidates) ||
                in_array($itemprop, $candidates)) {
                return $content;
            }
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

    public function getPosts(int $user_id, int $page = 1, int $per_page = 8): ?array
    {
        $calc_page = ($page - 1) * $per_page;
        return db()->fetchAll("SELECT uuid 
            FROM posts 
            WHERE created_at > NOW() - INTERVAL 30 DAY 
            AND parent_id IS NULL
            ORDER BY created_at DESC
            LIMIT ?,?", [$calc_page, $per_page]);
    }

    public function getUserPostCount(int $user_id)
    {
        return db()->execute("SELECT 1 
            FROM posts 
            WHERE user_id = ?", [$user_id])->rowCount();
    }

    public function getUserPosts(int $user_id, int $page = 1, int $per_page = 8): ?array
    {
        $calc_page = ($page - 1) * $per_page;
        return db()->fetchAll("SELECT uuid 
            FROM posts 
            WHERE created_at > NOW() - INTERVAL 30 DAY 
            AND parent_id IS NULL
            AND user_id = ?
            ORDER BY created_at DESC
            LIMIT ?,?", [$user_id, $calc_page, $per_page]);
    }

    public function getUserReplies(int $user_id, int $page = 1, int $per_page = 8)
    {
        $calc_page = ($page - 1) * $per_page;
        return db()->fetchAll("SELECT uuid 
            FROM posts 
            WHERE created_at > NOW() - INTERVAL 30 DAY 
            AND parent_id IS NOT NULL
            AND user_id = ?
            ORDER BY created_at DESC
            LIMIT ?,?", [$user_id, $calc_page, $per_page]);
    }

    public function getUserLikes(int $user_id, int $page = 1, int $per_page = 8)
    {
        $calc_page = ($page - 1) * $per_page;
        return db()->fetchAll("SELECT posts.uuid 
            FROM posts 
            INNER JOIN post_likes on post_id = posts.id AND post_likes.user_id = ?
            WHERE posts.created_at > NOW() - INTERVAL 30 DAY 
            AND posts.parent_id IS NULL
            ORDER BY post_likes.id DESC
            LIMIT ?,?", [$user_id, $calc_page, $per_page]);
    }

    public function getComments(string $uuid, int $page = 1, int $per_page = 8): ?array
    {
        $post = $this->getPostByUUID($uuid);
        if ($post) {
            $calc_page = ($page - 1) * $per_page;
            return db()->fetchAll("SELECT uuid 
                FROM posts 
                WHERE parent_id = ?
                ORDER BY created_at DESC
                LIMIT ?,?", [$post->id, $calc_page, $per_page]);
        }
        return null;
    }

    public function searchPosts(string $term)
    {
        return db()->fetchAll("SELECT posts.uuid 
            FROM posts 
            INNER JOIN users ON users.id = user_id
            WHERE (first_name LIKE ? OR surname LIKE ? OR username LIKE ? OR content LIKE ?)
            ORDER BY posts.created_at DESC", array_fill(0, 4, "%$term%"));
    }

    public function isLiked(int $user_id, string $uuid)
    {
        $post = $this->getPostByUUID($uuid);
        if ($post) {
            return $post->isLiked($user_id);
        }
        return null;
    }

    public function getLikeCount(string $uuid)
    {
        $post = $this->getPostByUUID($uuid);
        if ($post) {
            return $post->likeCount();
        }
        return null;
    }

    public function likePost(int $user_id, string $uuid)
    {
        $post = $this->getPostByUUID($uuid);
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
