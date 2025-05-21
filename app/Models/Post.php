<?php

namespace App\Models;

use Echo\Framework\Database\Model;

class Post extends Model
{
    public function __construct(?string $id = null)
    {
        parent::__construct('posts', $id);
    }
}
