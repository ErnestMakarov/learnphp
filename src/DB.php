<?php

namespace App;

use InvalidArgumentException;
use PDO;

class DB
{
    private static ?PDO $connection = null;
    private const COLUMNS = [
        'articles' => ['id', 'title', 'body', 'created_at', 'updated_at'],
        'users' => ['id', 'name', 'email', 'password', 'is_admin', 'created_at', 'updated_at'],
    ];

    public static function connection(): PDO
    {
        if (self::$connection === null) {
            $dsn = getenv('DB_DSN') ?: 'sqlite:' . dirname(__DIR__) . '/storage/learnphp.sqlite';
            self::$connection = new PDO($dsn, getenv('DB_USER') ?: null, getenv('DB_PASSWORD') ?: null, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            if (self::$connection->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite') {
                self::$connection->exec('PRAGMA busy_timeout = 5000');
            }
        }
        return self::$connection;
    }

    private function checkColumns(string $table, array $columns = []): void
    {
        if (!isset(self::COLUMNS[$table]) || array_diff($columns, self::COLUMNS[$table])) {
            throw new InvalidArgumentException('Unknown table or column.');
        }
    }

    public function all(string $table, string $class): array
    {
        $this->checkColumns($table);
        $statement = self::connection()->query("SELECT * FROM $table ORDER BY id DESC");
        return $statement->fetchAll(PDO::FETCH_CLASS, $class);
    }

    public function find(string $table, string $class, int $id): ?object
    {
        return $this->where($table, $class, 'id', $id)[0] ?? null;
    }

    public function where(string $table, string $class, string $column, mixed $value): array
    {
        $this->checkColumns($table, [$column]);
        $statement = self::connection()->prepare("SELECT * FROM $table WHERE $column = ?");
        $statement->execute([$value]);
        return $statement->fetchAll(PDO::FETCH_CLASS, $class);
    }

    public function insert(string $table, array $fields): int
    {
        $this->checkColumns($table, array_keys($fields));
        $columns = implode(', ', array_keys($fields));
        $placeholders = implode(', ', array_fill(0, count($fields), '?'));
        $statement = self::connection()->prepare("INSERT INTO $table ($columns) VALUES ($placeholders)");
        $statement->execute(array_values($fields));
        return (int) self::connection()->lastInsertId();
    }

    public function update(string $table, array $fields, int $id): void
    {
        $this->checkColumns($table, array_keys($fields));
        $assignments = implode(', ', array_map(fn (string $column) => "$column = ?", array_keys($fields)));
        $statement = self::connection()->prepare(
            "UPDATE $table SET $assignments, updated_at = CURRENT_TIMESTAMP WHERE id = ?"
        );
        $statement->execute([...array_values($fields), $id]);
    }

    public function delete(string $table, int $id): void
    {
        $this->checkColumns($table);
        $statement = self::connection()->prepare("DELETE FROM $table WHERE id = ?");
        $statement->execute([$id]);
    }
}
