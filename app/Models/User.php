<?php

namespace App\Models;

use Echo\Framework\Database\Model;

class User extends Model
{
    public function __construct(?string $id = null)
    {
        parent::__construct('users', $id);
    }
}
