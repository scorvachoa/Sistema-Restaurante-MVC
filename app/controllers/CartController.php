<?php
require_once __DIR__ . '/../models/Product.php';

class CartController extends Controller
{
    private function cart(): array
    {
        return $_SESSION['cart'] ?? [];
    }

    private function saveCart(array $cart): void
    {
        $_SESSION['cart'] = $cart;
    }

    public function index(): void
    {
        $cart = $this->cart();
        $this->view('cart/index', compact('cart'));
    }

    public function add(): void
    {
        $productId = (int)($_POST['product_id'] ?? 0);
        $quantity = max(1, (int)($_POST['quantity'] ?? 1));
        $extras = $_POST['extras'] ?? [];

        $product = Product::find($productId);
        if (!$product) {
            $this->redirect('/menu');
        }

        $extraIds = array_values(array_filter(array_map('intval', $extras)));
        $validExtras = $extraIds ? Product::extrasByIds($productId, $extraIds) : [];

        $cart = $this->cart();
        $cartId = uniqid('item_', true);
        $cart[$cartId] = [
            'id' => $cartId,
            'product_id' => $productId,
            'name' => $product['name'],
            'unit_price' => (float)$product['price'],
            'quantity' => $quantity,
            'extras' => $validExtras,
        ];
        $this->saveCart($cart);
        $this->redirect('/cart');
    }

    public function update(): void
    {
        $itemId = $_POST['item_id'] ?? '';
        $quantity = max(1, (int)($_POST['quantity'] ?? 1));
        $cart = $this->cart();
        if (isset($cart[$itemId])) {
            $cart[$itemId]['quantity'] = $quantity;
            $this->saveCart($cart);
        }
        $this->redirect('/cart');
    }

    public function remove(): void
    {
        $itemId = $_POST['item_id'] ?? '';
        $cart = $this->cart();
        unset($cart[$itemId]);
        $this->saveCart($cart);
        $this->redirect('/cart');
    }

    private function mapExtras(array $extras): array
    {
        return [];
    }
}