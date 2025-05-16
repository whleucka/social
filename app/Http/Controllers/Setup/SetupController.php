<?php

namespace App\Http\Controllers\Setup;

use App\Providers\Setup\SetupService;
use Echo\Framework\Http\Controller;
use Echo\Framework\Routing\Route\{Get, Post};
use Echo\Framework\Session\Flash;

class SetupController extends Controller
{
    private $setup_rules = [
        "app_name" => ["required"],
        "app_url" => ["required"],
        "app_debug" => ["required"],
        "db_driver" => ["required"],
        "db_name" => ["required"],
        "db_username" => ["required"],
        "db_password" => ["required"],
        "db_host" => ["required"],
        "db_port" => ["required"],
        "db_charset" => ["required"],
    ];

    public function __construct(private SetupService $provider)
    {
        $this->isComplete();
    }

    private function isComplete(): void
    {
        // We should bail if setup is complete
        $is_complete = $this->provider->dbExists() && $this->provider->dbConnected() && $this->provider->tableExists('users');
        if ($is_complete) {
            Flash::add("warning", "Setup complete. For security purposes, the setup page has been disabled.");
            $this->redirect(uri("auth.sign-in.index"));
        }
    }

    #[Get("/setup", "setup.index")] 
    public function index(): string
    {
        return $this->render("setup/index.html.twig", [
            "app" => config('app'),
            "db" => config('db'),
            "config_exists" => $this->provider->configExists(),
            "db_exists" => $this->provider->dbExists(),
            "db_connected" => $this->provider->dbExists() ? $this->provider->dbConnected() : false,
            "migration_complete" => $this->provider->dbExists() && $this->provider->dbConnected() ? $this->provider->tableExists('users') : false,
        ]);
    }

    #[Post("/setup/config", "setup.config")] 
    public function config(): string
    {
        $valid = $this->validate($this->setup_rules);

        if ($valid) {
            if ($this->provider->writeConfig($valid)) {
                Flash::add("success", "Success! Configuration saved.");
            } else {
                Flash::add("danger", "Error! Failed to save configuration.");
            }
        }

        return $this->index();
    }

    #[Post("/setup/database/create", "setup.create-db")] 
    public function create_db(): string
    {
        $valid = $this->validate($this->setup_rules);

        if ($valid) {
            if ($this->provider->createDatabase()) {
                Flash::destroy();
                Flash::add("success", "Success! Database created.");
            } else {
                Flash::add("danger", "Error! Failed to create database.");
            }
        }

        return $this->index();
    }

    #[Post("/setup/database/drop", "setup.drop-db")] 
    public function drop_db(): string
    {
        $valid = $this->validate($this->setup_rules);

        if ($valid) {
            if ($this->provider->dropDatabase()) {
                Flash::add("success", "Success! Database destroyed.");
            } else {
                Flash::add("danger", "Error! Failed to destroy database.");
            }
        }

        return $this->index();
    }

    #[Post("/setup/database/migrate", "setup.migrate-db")] 
    public function migrate_db(): string
    {
        $valid = $this->validate($this->setup_rules);

        if ($valid) {
            if ($this->provider->migrateDatabase()) {
                Flash::add("success", "Success! Database migration complete.");
            } else {
                Flash::add("danger", "Error! Failed database migration.");
            }
        }

        return $this->index();
    }
}
