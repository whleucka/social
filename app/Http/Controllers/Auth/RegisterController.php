<?php

namespace App\Http\Controllers\Auth;

use Echo\Framework\Http\Controller;
use Echo\Framework\Routing\Route\Get;

class RegisterController extends Controller
{
    #[Get("/register", "auth.register")]
    public function index(): string
    {
        return $this->render("auth/register.html");
    }
}

