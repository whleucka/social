<?php

namespace App\Models;

use Echo\Framework\Database\Model;

class PostLike extends Model
{
    public function __construct(?string $id = null)
    {
        parent::__construct('post_likes', $id);
    }
}

