<?php

namespace Echo\Interface\Database;

interface Model
{
    public static function create(array $model_data);
    public static function all();
    public static function find(string $id);
    public static function where(string $field, string $operator = '=', ?string $value = null);
    public function orWhere(string $field, string $operator = '=', ?string $value = null);
    public function andWhere(string $field, string $operator = '=', ?string $value = null);
}
