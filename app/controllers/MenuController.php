<?php
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Product.php';

class MenuController extends Controller
{
    public function index(): void
    {
        $categories = Category::all();
        $categoryId = (int)($_GET['category'] ?? 0);
        $products = $categoryId ? Product::byCategory($categoryId) : Product::all();
        $this->view('menu/index', compact('categories', 'products', 'categoryId'));
    }

    public function show(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $product = Product::find($id);
        if (!$product) {
            http_response_code(404);
            echo 'Producto no encontrado';
            return;
        }
        $extras = Product::extras($id);
        $this->view('menu/show', compact('product', 'extras'));
    }
}
