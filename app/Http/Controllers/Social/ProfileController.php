<?php

namespace App\Http\Controllers\Social;

use App\Providers\Social\ProfileService;
use Echo\Framework\Http\Controller;
use Echo\Framework\Routing\Route\Get;

class ProfileController extends Controller
{
    public function __construct(private ProfileService $profile_provider)
    {
    }

    #[Get("/profile/{username}", "profile.index", ["auth"])]
    public function index(string $username): string
    {
        $user = $this->profile_provider->getUserByUsername($username);

        if ($user) {
            return $this->render("profile/index.html.twig", [
                "profile" => $user,
            ]);
        }

        $this->pageNotFound();
    }
}
