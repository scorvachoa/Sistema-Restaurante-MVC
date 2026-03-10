<?php
class Controller
{
    protected function view(string $path, array $data = []): void
    {
        extract($data);
        require __DIR__ . '/../app/views/partials/header.php';
        require __DIR__ . '/../app/views/' . $path . '.php';
        require __DIR__ . '/../app/views/partials/footer.php';
    }

    protected function viewRaw(string $path, array $data = []): void
    {
        extract($data);
        require __DIR__ . '/../app/views/' . $path . '.php';
    }

    protected function redirect(string $path): void
    {
        $base = App::config('app', 'base_url');
        header('Location: ' . $base . $path);
        exit;
    }
}
