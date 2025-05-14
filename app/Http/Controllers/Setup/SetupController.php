<?php

namespace App\Http\Controllers\Setup;

use Echo\Framework\Http\Controller;
use Echo\Framework\Routing\Route\{Get, Post};

class SetupController extends Controller
{
    #[Get("/setup", "setup.index")] 
    public function index(): string
    {
        return $this->render("setup/index.html.twig");
    }

    #[Post("/setup", "setup.post")] 
    public function post(): string
    {
        $valid = $this->validate([
            "app_name" => [],
            "app_url" => [],
            "app_debug" => [],
            "db_name" => [],
            "db_username" => [],
            "db_password" => [],
            "db_host" => [],
            "db_port" => [],
            "db_charset" => [],
        ]);

        if ($valid) {
            dd("WIP");
        }
        
        return $this->index();
    }
}
