<?php
class Payment
{
    public static function all(array $filters = []): array
    {
        $pdo = Database::getInstance();
        $where = [];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = 'p.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['method'])) {
            $where[] = 'p.method = ?';
            $params[] = $filters['method'];
        }

        $sql = 'SELECT p.*, o.user_id, u.name as user_name FROM payments p JOIN orders o ON o.id = p.order_id JOIN users u ON u.id = o.user_id';
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY p.created_at DESC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function find(int $paymentId): ?array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('SELECT * FROM payments WHERE id = ?');
        $stmt->execute([$paymentId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function updateStatus(int $paymentId, string $status): void
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('UPDATE payments SET status = ? WHERE id = ?');
        $stmt->execute([$status, $paymentId]);
    }
}