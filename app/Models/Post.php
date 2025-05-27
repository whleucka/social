<?php

namespace App\Models;

use Echo\Framework\Database\Model;
use Carbon\Carbon;

class Post extends Model
{
    public function __construct(?string $id = null)
    {
        parent::__construct('posts', $id);
    }

    public function getParent()
    {
        return $this->parent_id ? Post::find($this->parent_id) : null;
    }

    public function shortTimeDiff(Carbon $time, ?Carbon $now = null): string {
        $now = $now ?? Carbon::now();
        $diff = $time->diff($now);

        if ($diff->y > 0) return $diff->y . 'y';
        if ($diff->m > 0) return $diff->m . 'mo';
        if ($diff->d > 0) return $diff->d . 'd';
        if ($diff->h > 0) return $diff->h . 'h';
        if ($diff->i > 0) return $diff->i . 'm';
        return $diff->s . 's';
    }

    public function ago()
    {
        return $this->shortTimeDiff(Carbon::parse($this->created_at));
    }

    public function isLiked(int $user_id)
    {
        return PostLike::where("user_id", $user_id)
            ->andWhere("post_id", $this->id)->get();
    }

    public function likeCount(): int
    {
         $likes = PostLike::where("post_id", $this->id)->get(lazy: false) ?? [];
         return count($likes);
    }

    public function commentCount(): int
    {
         $comments = Post::where("parent_id", $this->id)->get(lazy: false) ?? [];
         return count($comments);
    }

    public function user()
    {
        return User::find($this->user_id);
    }
}
