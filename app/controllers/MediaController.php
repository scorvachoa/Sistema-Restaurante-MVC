<?php
class MediaController extends Controller
{
    public function productImage(): void
    {
        $file = $_GET['file'] ?? '';
        $file = basename($file);
        if ($file === '') {
            http_response_code(404);
            echo 'Archivo no encontrado';
            return;
        }

        $path = App::config('app', 'product_uploads_dir') . '/' . $file;
        if (!is_file($path)) {
            http_response_code(404);
            echo 'Archivo no encontrado';
            return;
        }

        $mime = mime_content_type($path) ?: 'application/octet-stream';
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($path));
        readfile($path);
    }
}