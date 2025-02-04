<?php

namespace App\Http\Controllers\Auth;

use Echo\Framework\Http\Controller;
use Echo\Framework\Routing\Route\Get;

class SignInController extends Controller
{
    #[Get("/sign-in", "auth.sign-in")]
    public function index(): string
    {
        return $this->render("auth/sign-in.html");
    }
}
