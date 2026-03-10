<?php
class Router
{
    private array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, callable $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(string $uri, string $method): void
    {
        $path = parse_url($uri, PHP_URL_PATH);
        $base = App::config('app', 'base_url');
        if (str_starts_with($path, $base)) {
            $path = substr($path, strlen($base));
        }
        if ($path === '' || $path === false) {
            $path = '/';
        }
        if ($path !== '/' && str_ends_with($path, '/')) {
            $path = rtrim($path, '/');
        }

        if ($method === 'POST') {
            verify_csrf();
        }

        $handler = $this->routes[$method][$path] ?? null;
        if ($handler) {
            call_user_func($handler);
            return;
        }

        http_response_code(404);
        echo '404 Not Found';
    }
}