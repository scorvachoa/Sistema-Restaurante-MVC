<?php
class Address
{
    public static function create(int $userId, array $data): int
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('INSERT INTO addresses (user_id, address, latitude, longitude) VALUES (?, ?, ?, ?)');
        $stmt->execute([$userId, $data['address'], $data['lat'], $data['lng']]);
        return (int)$pdo->lastInsertId();
    }

    public static function byUser(int $userId): array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('SELECT * FROM addresses WHERE user_id = ? ORDER BY created_at DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function findByUser(int $addressId, int $userId): ?array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('SELECT * FROM addresses WHERE id = ? AND user_id = ?');
        $stmt->execute([$addressId, $userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}