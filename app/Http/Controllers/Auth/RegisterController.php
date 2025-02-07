<?php

namespace App\Http\Controllers\Auth;

use App\Providers\Auth\RegisterService;
use Echo\Framework\Http\Controller;
use Echo\Framework\Routing\Route\{Get, Post};

class RegisterController extends Controller
{
    public function __construct(private RegisterService $provider)
    {
    }

    #[Get("/register", "auth.register.index")]
    public function index(): string
    {
        return $this->render("auth/register.html.twig");
    }

    #[Post("/register", "auth.register.post")]
    public function post()
    {
        $valid = $this->validate([
            "name" => ["required"],
            "email" => ["required", "email"],
            "password" => ["required"],
        ]);
        if ($valid) {
            $success = $this->provider->register($valid->name, $valid->email, $valid->password);
            if ($success) {
                die("wip: register success");
            }
        }
        return $this->index();
    }
}
