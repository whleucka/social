<?php

namespace App\Models;

use Echo\Framework\Database\Model;

class User extends Model
{
    public function __construct(?string $id = null)
    {
        parent::__construct('users', $id);
    }

    public function gravatarUrl($size = 120, $default = 'mp', $rating = 'g'): string
    {
        $email = strtolower(trim($this->email));
        $hash = md5($email);
        return "https://www.gravatar.com/avatar/$hash?s=$size&d=$default&r=$rating";
    }
}
