<?php

namespace App\Http\Controllers\Auth;

use App\Providers\Auth\RegisterService;
use Echo\Framework\Http\Controller;
use Echo\Framework\Routing\Route\{Get, Post};
use Echo\Framework\Session\Flash;

class RegisterController extends Controller
{
    public function __construct(private RegisterService $provider)
    {
    }

    #[Get("/register", "auth.register.index")]
    public function index(): string
    {
        return $this->render("auth/register/index.html.twig");
    }

    #[Post("/register", "auth.register.post", ["max_requests" => 40])]
    public function post(): string
    {
        $this->setValidationMessage("first_name.max_length", "First name can only contain letters, numbers, and underscore characters");
        $this->setValidationMessage("surname.max_length", "Surname can only contain letters, numbers, and underscore characters");
        $this->setValidationMessage("username.regex", "Username can only contain letters, numbers, and underscore characters");
        $this->setValidationMessage("username.max_length", "Max length is 20 characters");
        $this->setValidationMessage("password.min_length", "Must be at least 8 characters");
        $this->setValidationMessage("password.regex", "Must contain 1 upper case, 1 digit, 1 symbol");
        $this->setValidationMessage("password_match.match", "Password does not match");
        $valid = $this->validate([
            "first_name" => ["required", "max_length:20"],
            "surname" => ["required", "max_length:20"],
            "username" => ["required", "max_length:20", "regex:^[a-zA-Z0-9]+$", "unique:users"],
            "email" => ["required", "email", "unique:users"],
            "password" => ["required", "min_length:8", "regex:^(?=.*[A-Z])(?=.*\W)(?=.*\d).+$"],
            "password_match" => ["required", "match:password"],
        ]);
        if ($valid) {
            $success = $this->provider->register($valid->first_name, $valid->surname, $valid->username, $valid->email, $valid->password);
            if ($success) {
                $path = config("security.authenticated_route");
                header("HX-Redirect: $path");
            } else {
                Flash::add("warning", "Failed to register new account");
            }
        }
        return $this->index();
    }
}
