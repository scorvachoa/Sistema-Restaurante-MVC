<?php
function asset(string $path): string
{
    return App::config('app', 'base_url') . '/assets/' . ltrim($path, '/');
}

function url(string $path): string
{
    return App::config('app', 'base_url') . $path;
}

function auth_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_role(array $roles): void
{
    $user = auth_user();
    if (!$user || !in_array($user['role'], $roles, true)) {
        header('Location: ' . App::config('app', 'base_url') . '/login');
        exit;
    }
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!$token || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(419);
        echo 'CSRF token inválido';
        exit;
    }
}

function rate_limit(string $key, int $max, int $windowSeconds): bool
{
    $now = time();
    if (!isset($_SESSION['rate_limit'][$key])) {
        $_SESSION['rate_limit'][$key] = ['count' => 1, 'start' => $now];
        return true;
    }

    $entry = &$_SESSION['rate_limit'][$key];
    if ($now - $entry['start'] > $windowSeconds) {
        $entry = ['count' => 1, 'start' => $now];
        return true;
    }

    $entry['count']++;
    return $entry['count'] <= $max;
}

function app_log(string $message): void
{
    $dir = __DIR__ . '/../storage/logs';
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    $line = date('Y-m-d H:i:s') . ' ' . $message . PHP_EOL;
    file_put_contents($dir . '/app.log', $line, FILE_APPEND);
}

function status_label(string $status): string
{
    $map = [
        'pending' => 'Pendiente',
        'confirmed' => 'Confirmado',
        'assigned' => 'Asignado',
        'picked_up' => 'Recogido',
        'on_the_way' => 'En camino',
        'delivered' => 'Entregado',
        'cancelled' => 'Cancelado',
        'review' => 'Revisión',
        'approved' => 'Aprobado',
        'rejected' => 'Rechazado',
    ];
    return $map[$status] ?? $status;
}

function haversine_km(float $lat1, float $lon1, float $lat2, float $lon2): float
{
    $earthRadius = 6371.0;
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    return $earthRadius * $c;
}

function product_image_url(?string $image): string
{
    if (!$image) {
        return '';
    }
    if (preg_match('#^https?://#', $image) || str_starts_with($image, '/')) {
        return $image;
    }
    return App::config('app', 'base_url') . '/media/products?file=' . urlencode($image);
}