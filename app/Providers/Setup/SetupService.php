<?php

namespace App\Providers\Setup;

class SetupService
{
    public function writeConfig(object $config): int|false
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
        return file_put_contents($this->getEnvPath(), $env);
    }

    public function configExists(): bool
    {
        return file_exists($this->getEnvPath());
    }

    public function dbExists(): bool
    {
        $db = config("db");
        $cmd = sprintf("mysql -u %s -p%s -qfsBe \"SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME='%s'\"", $db['username'], $db['password'], $db['name']);
        exec($cmd, $output, $result_code);
        return !empty($output);
    }

    public function tableExists(string $table): bool
    {
        $db = config("db");
        $cmd = sprintf("mysql -u %s -p%s -qfsBe \"SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA='%s' AND TABLE_NAME='%s' LIMIT 1\"", $db['username'], $db['password'], $db['name'], $table);
        exec($cmd, $output, $result_code);
        return !empty($output);
    }

    public function dbConnected(): bool
    {
        return db(false)?->tryConnection() ?? false;
    }

    public function createDatabase(): bool
    {
        $db = config("db");
        $cmd = sprintf("mysql -u %s -p%s -e 'CREATE DATABASE IF NOT EXISTS %s'", $db['username'], $db['password'], $db['name']);
        exec($cmd, $output, $result_code);
        return $this->dbExists();
    }

    public function dropDatabase(): bool
    {
        $db = config("db");
        $cmd = sprintf("mysql -u %s -p%s -e 'DROP DATABASE IF EXISTS %s'", $db['username'], $db['password'], $db['name']);
        exec($cmd, $output, $result_code);
        return !$this->dbExists();
    }

    public function migrateDatabase(): bool
    {
        $cmd = "../bin/console migrate fresh";
        exec($cmd, $output, $result_code);
        return $this->tableExists('users');
    }

    private function getEnvPath(): string
    {
        $root_dir = config("paths.root");
        return $root_dir . '.env';
    }
}
