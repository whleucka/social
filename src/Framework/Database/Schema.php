<?php

namespace Echo\Framework\Database;

use Closure;

class Schema
{
    public static function create(string $table_name, Closure $callback): string
    {
        $blueprint = new Blueprint();
        $callback($blueprint);
        $sql = sprintf(
            "CREATE TABLE IF NOT EXISTS %s (%s)",
            $table_name,
            $blueprint->getDefinitions()
        );
        return $sql;
    }

    public static function drop(string $table_name): string
    {
        return sprintf("DROP TABLE IF EXISTS %s", $table_name);
    }
}
