<?php

namespace Echo\Framework\Database;

use Echo\Interface\Database\Model as DatabaseModel;
use Exception;
use PDO;

class Model implements DatabaseModel
{
    protected string $primary_key = "id";
    protected bool $auto_increment = true;
    protected array $columns = ["*"];
    protected QueryBuilder $qb;
    private array $where = [];
    private array $or_where = [];
    private array $order_by = [];
    private array $params = [];
    private array $attributes = [];
    private array $valid_operators = [
        "=",
        "!=",
        ">",
        ">=",
        "<",
        "<=",
        "is",
        "not",
        "like",
    ];

    public function __construct(private string $table_name, private ?string $id = null)
    {
        // Initialize the query builder
        $this->qb = new QueryBuilder();

        if (!is_null($id)) {
            $this->loadAttributes($id);
            if (empty($this->attributes)) {
                throw new Exception("model not found");
            }
        }
    }

    private function loadAttributes(string $id): void
    {
        $key = $this->primary_key;
        $result = $this->qb
            ->select($this->columns)
            ->from($this->table_name)
            ->where(["$key = ?"], $id)
            ->execute()
            ->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $this->attributes = $result;
        }
    }

    public static function create(array $data): static|bool
    {
        $class = get_called_class();
        $model = new $class();
        $result = $model->qb
            ->insert($data)
            ->into($model->table_name)
            ->params(array_values($data))
            ->execute();
        if ($result && $model->auto_increment) {
            $id = db()->lastInsertId();
            return self::find($id);
        } elseif ($result && !$model->auto_increment) {
            return true;
        }
        return false;
    }

    public static function find(string $id): ?static
    {
        $class = get_called_class();
        $model = new $class();
        try {
            $result = new $model($id);
            return $result;
        } catch (Exception) {
            return null;
        }
    }

    public static function where(string $field, string $operator = '=', ?string $value = null): static
    {
        $class = get_called_class();
        $model = new $class();

        // Default operator is =
        if (!in_array(strtolower($operator), $model->valid_operators)) {
            $value = $operator;
            $operator = "=";
        }
        // Add the where clause and params
        $model->where[] = "($field $operator ?)";
        $model->params[] = $value;
        return $model;
    }

    public function orWhere(string $field, string $operator = '=', ?string $value = null): Model
    {
        // Default operator is =
        if (!in_array(strtolower($operator), $this->valid_operators)) {
            $value = $operator;
            $operator = "=";
        }
        // Add the where clause and params
        $this->or_where[] = "($field $operator ?)";
        $this->params[] = $value;
        return $this;
    }

    public function andWhere(string $field, string $operator = '=', ?string $value = null): Model
    {
        // Default operator is =
        if (!in_array(strtolower($operator), $this->valid_operators)) {
            $value = $operator;
            $operator = "=";
        }
        // Add the where clause and params
        $this->where[] = "($field $operator ?)";
        $this->params[] = $value;
        return $this;
    }

    public function orderBy(string $column, string $direction = "ASC"): Model
    {
        $this->order_by[] = "$column $direction";
        return $this;
    }

    public function refresh(): Model
    {
        $this->loadAttributes($this->id);
        return $this;
    }

    public function get(
        int $limit = 0,
        bool $lazy = true
    ): null|array|static {
        $results = $this->qb
            ->select($this->columns)
            ->from($this->table_name)
            ->where($this->where)
            ->orWhere($this->or_where)
            ->orderBy($this->order_by)
            ->limit($limit)
            ->params($this->params)
            ->execute()
            ->fetchAll(PDO::FETCH_OBJ);
        $key = $this->primary_key;
        if ($results && $lazy && count($results) === 1) {
            $result = $results[0];
            return self::find($result->$key);
        }
        return $results
            ? array_map(fn ($result) => $this->find($result->$key), $results)
            : null;
    }

    public function first(): ?Model
    {
        $results = $this->qb
            ->select($this->columns)
            ->from($this->table_name)
            ->where($this->where)
            ->orWhere($this->or_where)
            ->orderBy($this->order_by)
            ->params($this->params)
            ->execute()
            ->fetchAll(PDO::FETCH_OBJ);
        $key = $this->primary_key;
        if ($results) {
            $result = $results[0];
            return self::find($result->$key);
        }
        return null;
    }

    public function last(): ?Model
    {
        $results = $this->qb
            ->select($this->columns)
            ->from($this->table_name)
            ->where($this->where)
            ->orWhere($this->or_where)
            ->orderBy($this->order_by)
            ->params($this->params)
            ->execute()
            ->fetchAll(PDO::FETCH_OBJ);
        $key = $this->primary_key;
        if ($results) {
            $result = end($results);
            return self::find($result->$key);
        }
        return null;
    }

    public function sql(int $limit = 1): array
    {
        $qb = $this->qb
            ->select($this->columns)
            ->from($this->table_name)
            ->where($this->where)
            ->orWhere($this->or_where)
            ->orderBy($this->order_by)
            ->params($this->params);
        return ["query" => $qb->getQuery(), "params" => $qb->getQueryParams()];
    }

    public function save(): Model
    {
        $key = $this->primary_key;
        $params = [...array_values($this->attributes), $this->id];
        $result = $this->qb
            ->update($this->attributes)
            ->table($this->table_name)
            ->where(["$key = ?"])
            ->params($params)
            ->execute();
        if ($result) {
            $this->loadAttributes($this->id);
        }
        return $this;
    }

    public function update(array $data): Model
    {
        $key = $this->primary_key;
        $params = [...array_values($data), $this->id];
        $result = $this->qb
            ->update($data)
            ->table($this->table_name)
            ->where(["$key = ?"])
            ->params($params)
            ->execute();
        if ($result) {
            $this->loadAttributes($this->id);
        }
        return $this;
    }

    public function delete(): bool
    {
        $key = $this->primary_key;
        $result = $this->qb
            ->delete()
            ->from($this->table_name)
            ->where(["$key = ?"], $this->id)
            ->execute();
        return (bool) $result;
    }

    public function __set($name, $value)
    {
        return $this->attributes[$name] = $value;
    }

    public function __get($name)
    {
        return $this->attributes[$name];
    }
}
