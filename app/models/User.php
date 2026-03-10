<?php
class User
{
    public static function create(string $name, string $email, string $password, string $role = 'client'): int
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password, role_id) VALUES (?, ?, ?, (SELECT id FROM roles WHERE name = ?))');
        $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), $role]);
        return (int)$pdo->lastInsertId();
    }

    public static function findByEmail(string $email): ?array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('SELECT u.id, u.name, u.email, r.name as role FROM users u JOIN roles r ON r.id = u.role_id WHERE u.email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function verifyLogin(string $email, string $password): ?array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('SELECT u.id, u.name, u.email, u.password, r.name as role FROM users u JOIN roles r ON r.id = u.role_id WHERE u.email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']);
            return $user;
        }
        return null;
    }

    public static function allByRole(string $role): array
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('SELECT u.id, u.name, u.email FROM users u JOIN roles r ON r.id = u.role_id WHERE r.name = ?');
        $stmt->execute([$role]);
        return $stmt->fetchAll();
    }
}
