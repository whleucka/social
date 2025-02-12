<?php

namespace App\Providers\Auth;

use App\Models\User;

class RegisterService
{
    public function register(string $first_name, string $surname, string $email, string $password): bool|User
    {
        $user = User::create([
            "first_name" => $first_name,
            "surname" => $surname,
            "email" => $email,
            "password" => password_hash($password, PASSWORD_ARGON2I),
        ]);

        if ($user) {
            session()->set("user_uuid", $user->uuid);
            return $user;
        }

        return false;
    }
}
