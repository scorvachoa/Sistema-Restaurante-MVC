<?php
class Order
{
    public static function create(int $userId, array $payload): int
    {
        $pdo = Database::getInstance();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare('INSERT INTO orders (user_id, address_id, status, delivery_cost, total_amount, notes) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([
                $userId,
                $payload['address_id'],
                'pending',
                $payload['delivery_cost'],
                $payload['total_amount'],
                $payload['notes'] ?? ''
            ]);
            $orderId = (int)$pdo->lastInsertId();

            $itemStmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)');
            $extraStmt = $pdo->prepare('INSERT INTO order_item_extras (order_item_id, extra_id, extra_price) VALUES (?, ?, ?)');

            foreach ($payload['items'] as $item) {
                $productId = (int)$item['product_id'];
                $quantity = (int)$item['quantity'];

                $stockStmt = $pdo->prepare('SELECT stock FROM products WHERE id = ? FOR UPDATE');
                $stockStmt->execute([$productId]);
                $row = $stockStmt->fetch();
                if (!$row || (int)$row['stock'] < $quantity) {
                    throw new RuntimeException('Sin stock suficiente');
                }

                $updateStock = $pdo->prepare('UPDATE products SET stock = stock - ? WHERE id = ?');
                $updateStock->execute([$quantity, $productId]);

                $itemStmt->execute([$orderId, $productId, $quantity, $item['unit_price']]);
                $orderItemId = (int)$pdo->lastInsertId();
                foreach ($item['extras'] as $extra) {
                    $extraStmt->execute([$orderItemId, $extra['id'], $extra['price']]);
                }
            }

            $payStmt = $pdo->prepare('INSERT INTO payments (order_id, method, status, proof_image) VALUES (?, ?, ?, ?)');
            $payStmt->execute([$orderId, $payload['payment_method'], 'pending', $payload['payment_proof']]);

            $pdo->commit();
            return $orderId;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    public static function byUser(int $userId): array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function find(int $orderId): ?array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('SELECT o.*, u.name as user_name FROM orders o JOIN users u ON u.id = o.user_id WHERE o.id = ?');
        $stmt->execute([$orderId]);
        $order = $stmt->fetch();
        return $order ?: null;
    }

    public static function items(int $orderId): array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('SELECT oi.*, p.name as product_name FROM order_items oi JOIN products p ON p.id = oi.product_id WHERE oi.order_id = ?');
        $stmt->execute([$orderId]);
        $items = $stmt->fetchAll();

        $extraStmt = $pdo->prepare('SELECT oie.*, pe.name as extra_name FROM order_item_extras oie JOIN product_extras pe ON pe.id = oie.extra_id WHERE oie.order_item_id = ?');
        foreach ($items as &$item) {
            $extraStmt->execute([$item['id']]);
            $item['extras'] = $extraStmt->fetchAll();
        }
        return $items;
    }

    public static function all(array $filters = []): array
    {
        $pdo = Database::getInstance();
        $where = [];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = 'o.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['q'])) {
            $where[] = '(o.id = ? OR u.name LIKE ?)';
            $params[] = (int)$filters['q'];
            $params[] = '%' . $filters['q'] . '%';
        }

        $sql = 'SELECT o.*, u.name as user_name FROM orders o JOIN users u ON u.id = o.user_id';
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY o.created_at DESC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function canTransition(string $from, string $to): bool
    {
        $map = [
            'pending' => ['confirmed', 'cancelled'],
            'confirmed' => ['assigned', 'cancelled'],
            'assigned' => ['picked_up', 'cancelled'],
            'picked_up' => ['on_the_way'],
            'on_the_way' => ['delivered'],
            'delivered' => [],
            'cancelled' => [],
        ];
        return in_array($to, $map[$from] ?? [], true);
    }

    public static function updateStatus(int $orderId, string $status): bool
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('SELECT status FROM orders WHERE id = ?');
        $stmt->execute([$orderId]);
        $current = $stmt->fetchColumn();
        if (!$current || !self::canTransition($current, $status)) {
            return false;
        }

        $update = $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?');
        $update->execute([$status, $orderId]);
        return true;
    }

    public static function assignRider(int $orderId, int $riderId): void
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('UPDATE orders SET rider_id = ?, status = ? WHERE id = ?');
        $stmt->execute([$riderId, 'assigned', $orderId]);

        $checkStmt = $pdo->prepare('SELECT id FROM deliveries WHERE order_id = ?');
        $checkStmt->execute([$orderId]);
        $delivery = $checkStmt->fetch();

        if ($delivery) {
            $updateStmt = $pdo->prepare('UPDATE deliveries SET rider_id = ?, status = ? WHERE id = ?');
            $updateStmt->execute([$riderId, 'assigned', $delivery['id']]);
        } else {
            $deliveryStmt = $pdo->prepare('INSERT INTO deliveries (order_id, rider_id, status) VALUES (?, ?, ?)');
            $deliveryStmt->execute([$orderId, $riderId, 'assigned']);
        }
    }

    public static function assignedTo(int $riderId): array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('SELECT o.*, u.name as user_name FROM orders o JOIN users u ON u.id = o.user_id WHERE o.rider_id = ? ORDER BY o.created_at DESC');
        $stmt->execute([$riderId]);
        return $stmt->fetchAll();
    }
}