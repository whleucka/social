<?php

namespace App\Providers\Auth;

use App\Models\User;

class SignInService
{
    public function signIn(string $email_address, string $password): bool
    {
        $user = User::where("email", $email_address)->get();

        if ($user) {
            # WIP: set user session
            return password_verify($password, $user->password);
        }

        return false;
    }
}
