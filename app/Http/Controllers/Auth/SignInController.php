<?php

namespace App\Http\Controllers\Auth;

use App\Providers\Auth\SignInServiceProvider;
use Echo\Framework\Http\Controller;
use Echo\Framework\Routing\Route\{Get, Post};

class SignInController extends Controller
{
    public function __construct(private SignInServiceProvider $service)
    {
    }

    #[Get("/sign-in", "auth.sign-in.index")]
    public function index(): string
    {
        return $this->render("auth/sign-in.html.twig");
    }

    #[Post("/sign-in", "auth.sign-in.post")]
    public function post()
    {
        die("wip");
    }
}
