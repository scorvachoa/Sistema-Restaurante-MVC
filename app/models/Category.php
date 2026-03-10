<?php
class Category
{
    public static function all(): array
    {
        $pdo = Database::getInstance();
        return $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();
    }
}
