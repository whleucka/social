<?php

namespace App\Http\Controllers\Social;

use Echo\Framework\Http\Controller;
use Echo\Framework\Routing\Route\Get;

class SettingsController extends Controller
{
    #[Get("/settings", "settings.index", ["auth"])]
    public function index(): string
    {
        return $this->render("settings/index.html.twig", []);
    }
}
