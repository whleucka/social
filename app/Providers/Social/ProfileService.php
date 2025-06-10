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
                "id" => $user->id,
                "gravatar" => $user->gravatarUrl(),
                "name" => $user->first_name . ' ' . $user->surname,
                "username" => $user->username,
                "first_name" => $user->first_name,
                "surname" => $user->surname,
                "description" => $user->description,
            ];
        }
        return null;
    }

    public function save(string $_username, string $first_name, string $surname, string $username, ?string $description)
    {
        $user = User::where("username", $_username)->get();
        if ($user) {
            $user->first_name = $first_name;
            $user->surname = $surname;
            $user->username = $username;
            $user->description = $description;
            $user->save();
        }
    }
}
