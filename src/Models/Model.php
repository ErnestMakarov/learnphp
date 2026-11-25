<?php

namespace App\Models;

use App\DB;

abstract class Model
{
    protected static string $table;
    public ?int $id = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    public static function all(): array
    {
        return (new DB())->all(static::$table, static::class);
    }

    public static function find(int $id): ?static
    {
        return (new DB())->find(static::$table, static::class, $id);
    }

    public static function where(string $column, mixed $value): array
    {
        return (new DB())->where(static::$table, static::class, $column, $value);
    }

    public function save(): void
    {
        $fields = get_object_vars($this);
        unset($fields['id'], $fields['created_at'], $fields['updated_at']);
        $db = new DB();
        if ($this->id !== null) {
            $db->update(static::$table, $fields, $this->id);
        } else {
            $this->id = $db->insert(static::$table, $fields);
        }
    }

    public function delete(): void
    {
        (new DB())->delete(static::$table, $this->id);
    }
}
