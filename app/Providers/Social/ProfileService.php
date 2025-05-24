<?php

namespace App\Providers\Social;

use App\Models\User;

class ProfileService
{
    public function getUserByUsername(string $username)
    {
        $user = User::where("username", $username)->get();
        if ($user) {
            return [
                "gravatar" => $user->gravatarUrl(),
                "name" => $user->first_name . ' ' . $user->surname,
                "username" => $user->username,
            ];
        }
        return null;
    }
}
