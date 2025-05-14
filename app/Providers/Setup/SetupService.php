<?php

namespace App\Providers\Setup;

class SetupService
{
    public function writeConfig(object $config): void
    {
        $env =<<<CONFIG
APP_NAME={$config->app_name}
APP_URL={$config->app_url}
APP_DEBUG={$config->app_debug}

DEV_SERVER=0.0.0.0
DEV_PORT=8000

DB_NAME={$config->db_name}
DB_USERNAME={$config->db_username}
DB_PASSWORD={$config->db_password}
DB_HOST={$config->db_host}
DB_PORT={$config->db_port}
DB_CHARSET={$config->db_charset}
CONFIG;
        file_put_contents($this->getEnvPath(), $env);
    }

    public function configExists(): bool
    {
        return file_exists($this->getEnvPath());
    }

    public function dbConnected(): bool
    {
        $this->createDatabase();
        return $this->configExists() && db()?->isConnected();
    }

    public function createDatabase(): void
    {
        $db = config("db");
        exec("mysql -u {$db['username']} -p{$db['password']} -e 'CREATE DATABASE IF NOT EXISTS {$db['name']}'", $output, $result_code);
        db()?->execute("USE {$db['name']}");
    }

    private function getEnvPath(): string
    {
        $root_dir = config("paths.root");
        return $root_dir . '.env';
    }
}
