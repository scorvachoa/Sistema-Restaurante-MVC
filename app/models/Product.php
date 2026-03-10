<?php
class Product
{
    public static function all(): array
    {
        $pdo = Database::getInstance();
        $sql = 'SELECT p.*, c.name as category_name FROM products p JOIN categories c ON c.id = p.category_id ORDER BY p.name';
        return $pdo->query($sql)->fetchAll();
    }

    public static function byCategory(int $categoryId): array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('SELECT p.*, c.name as category_name FROM products p JOIN categories c ON c.id = p.category_id WHERE p.category_id = ? ORDER BY p.name');
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function extras(int $productId): array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('SELECT * FROM product_extras WHERE product_id = ? ORDER BY name');
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    public static function extrasByIds(int $productId, array $extraIds): array
    {
        $extraIds = array_values(array_unique(array_filter($extraIds, 'is_int')));
        if (!$extraIds) {
            return [];
        }
        $pdo = Database::getInstance();
        $placeholders = implode(',', array_fill(0, count($extraIds), '?'));
        $sql = "SELECT id, name, price FROM product_extras WHERE product_id = ? AND id IN ($placeholders) ORDER BY name";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_merge([$productId], $extraIds));
        return $stmt->fetchAll();
    }

    public static function create(array $data): void
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('INSERT INTO products (name, price, description, image, stock, category_id) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$data['name'], $data['price'], $data['description'], $data['image'], $data['stock'], $data['category_id']]);
    }

    public static function update(int $id, array $data): void
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('UPDATE products SET name = ?, price = ?, description = ?, image = ?, stock = ?, category_id = ? WHERE id = ?');
        $stmt->execute([$data['name'], $data['price'], $data['description'], $data['image'], $data['stock'], $data['category_id'], $id]);
    }

    public static function delete(int $id): void
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
        $stmt->execute([$id]);
    }
}