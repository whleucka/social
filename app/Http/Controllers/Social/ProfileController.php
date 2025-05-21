<?php

namespace App\Http\Controllers\Social;

use Echo\Framework\Http\Controller;
use Echo\Framework\Routing\Route\Get;

class ProfileController extends Controller
{
    #[Get("/profile", "profile.index", ["auth"])]
    public function index(): string
    {
        return $this->render("profile/index.html.twig", []);
    }
}
