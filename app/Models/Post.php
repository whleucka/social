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

    public function ago()
    {
        return Carbon::parse($this->created_at)->diffForHumans();
    }

    public function isLiked(int $user_id)
    {
        return PostLike::where("user_id", $user_id)
            ->andWhere("post_id", $this->id)->get();
    }

    public function user()
    {
        return User::find($this->user_id);
    }
}
