<?php

namespace App\Http\Controllers\Setup;

use App\Providers\Setup\SetupService;
use Echo\Framework\Http\Controller;
use Echo\Framework\Routing\Route\{Get, Post};
use Echo\Framework\Session\Flash;

class SetupController extends Controller
{
    public function __construct(private SetupService $provider)
    {
    }

    #[Get("/setup", "setup.index")] 
    public function index(): string
    {
        return $this->render("setup/index.html.twig", [
            "app" => config('app'),
            "db" => config('db'),
            "config_exists" => $this->provider->configExists(),
            "db_exists" => $this->provider->dbExists(),
        ]);
    }

    #[Post("/setup", "setup.config")] 
    public function config(): string
    {
        $valid = $this->validate([
            "app_name" => ["required"],
            "app_url" => ["required"],
            "app_debug" => ["required"],
            "db_name" => ["required"],
            "db_username" => ["required"],
            "db_password" => ["required"],
            "db_host" => ["required"],
            "db_port" => ["required"],
            "db_charset" => ["required"],
        ]);

        if ($valid) {
            if ($this->provider->writeConfig($valid)) {
                Flash::add("success", "Success! Configuration saved.");
            }
        }

        return $this->index();
    }
}
