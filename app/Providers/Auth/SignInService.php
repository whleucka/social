<?php

namespace App\Providers\Auth;

use App\Models\User;

class SignInService
{
    public function signIn(string $email_address, string $password): bool
    {
        $user = User::where("email", $email_address)
            ->andWhere("bot", 0)->get();

        if ($user && password_verify($password, $user->password)) {
            session()->set("user_uuid", $user->uuid);
            return true;
        }

        return false;
    }
}
