<?php
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Address.php';
require_once __DIR__ . '/../models/Product.php';

class OrderController extends Controller
{
    public function checkout(): void
    {
        require_role(['client']);
        $cart = $_SESSION['cart'] ?? [];
        $addresses = Address::byUser(auth_user()['id']);
        $pricePerKm = App::config('app', 'delivery_price_per_km');
        $this->view('orders/checkout', compact('cart', 'addresses', 'pricePerKm'));
    }

    public function create(): void
    {
        require_role(['client']);
        $cart = $_SESSION['cart'] ?? [];
        if (!$cart) {
            $this->redirect('/cart');
        }

        $addressId = (int)($_POST['address_id'] ?? 0);
        $lat = null;
        $lng = null;

        if ($addressId === 0) {
            $lat = is_numeric($_POST['lat'] ?? null) ? (float)$_POST['lat'] : null;
            $lng = is_numeric($_POST['lng'] ?? null) ? (float)$_POST['lng'] : null;
            $addressId = Address::create(auth_user()['id'], [
                'address' => trim($_POST['address'] ?? ''),
                'lat' => $lat,
                'lng' => $lng,
            ]);
        } else {
            $address = Address::findByUser($addressId, auth_user()['id']);
            if (!$address) {
                http_response_code(403);
                echo 'Acceso denegado';
                return;
            }
            $lat = is_numeric($address['latitude']) ? (float)$address['latitude'] : null;
            $lng = is_numeric($address['longitude']) ? (float)$address['longitude'] : null;
        }

        $deliveryCost = 0.0;
        $restaurantLat = App::config('app', 'restaurant_lat');
        $restaurantLng = App::config('app', 'restaurant_lng');
        if (is_numeric($restaurantLat) && is_numeric($restaurantLng) && $lat !== null && $lng !== null) {
            $distanceKm = haversine_km((float)$restaurantLat, (float)$restaurantLng, $lat, $lng);
            $pricePerKm = (float)App::config('app', 'delivery_price_per_km');
            $deliveryCost = $distanceKm * $pricePerKm;
        }

        $items = [];
        $subtotal = 0.0;
        foreach ($cart as $item) {
            $product = Product::find((int)$item['product_id']);
            if (!$product) {
                $_SESSION['flash_error'] = 'Un producto del carrito ya no existe.';
                $this->redirect('/cart');
            }

            $extraIds = [];
            foreach ($item['extras'] as $extra) {
                if (is_array($extra) && isset($extra['id'])) {
                    $extraIds[] = (int)$extra['id'];
                } else {
                    $extraIds[] = (int)$extra;
                }
            }
            $validExtras = $extraIds ? Product::extrasByIds((int)$item['product_id'], $extraIds) : [];

            $extrasTotal = array_sum(array_column($validExtras, 'price'));
            $unitPrice = (float)$product['price'];
            $quantity = max(1, (int)$item['quantity']);
            $lineTotal = ($unitPrice + $extrasTotal) * $quantity;
            $subtotal += $lineTotal;

            $items[] = [
                'product_id' => (int)$product['id'],
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'extras' => $validExtras,
            ];
        }

        $paymentMethod = $_POST['payment_method'] ?? 'cash';
        $proofPath = null;
        if ($paymentMethod !== 'cash') {
            if (!isset($_FILES['payment_proof'])) {
                $_SESSION['flash_error'] = 'Sube el comprobante de pago.';
                $this->redirect('/checkout');
            }
            $proofPath = $this->saveProof($_FILES['payment_proof']);
            if ($proofPath === null) {
                $_SESSION['flash_error'] = 'El comprobante es inválido.';
                $this->redirect('/checkout');
            }
        }

        try {
            $orderId = Order::create(auth_user()['id'], [
                'address_id' => $addressId,
                'delivery_cost' => $deliveryCost,
                'total_amount' => $subtotal + $deliveryCost,
                'items' => $items,
                'payment_method' => $paymentMethod,
                'payment_proof' => $proofPath,
                'notes' => trim($_POST['notes'] ?? ''),
            ]);
        } catch (Throwable $e) {
            app_log('Order create failed: ' . $e->getMessage());
            $_SESSION['flash_error'] = 'No se pudo crear el pedido. Revisa el stock.';
            $this->redirect('/cart');
        }

        $_SESSION['cart'] = [];
        $this->redirect('/order?id=' . $orderId);
    }

    public function history(): void
    {
        require_role(['client']);
        $orders = Order::byUser(auth_user()['id']);
        $this->view('orders/history', compact('orders'));
    }

    public function show(): void
    {
        require_role(['client', 'admin', 'rider']);
        $orderId = (int)($_GET['id'] ?? 0);
        $order = Order::find($orderId);
        if (!$order) {
            http_response_code(404);
            echo 'Pedido no encontrado';
            return;
        }

        $user = auth_user();
        if ($user['role'] === 'client' && (int)$order['user_id'] !== (int)$user['id']) {
            http_response_code(403);
            echo 'Acceso denegado';
            return;
        }
        if ($user['role'] === 'rider' && (int)$order['rider_id'] !== (int)$user['id']) {
            http_response_code(403);
            echo 'Acceso denegado';
            return;
        }

        $items = Order::items($orderId);
        $this->view('orders/show', compact('order', 'items'));
    }

    private function saveProof(array $file): ?string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            return null;
        }

        $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
        $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt, true)) {
            return null;
        }

        $info = @getimagesize($file['tmp_name']);
        if (!$info || !in_array($info['mime'], $allowedMime, true)) {
            return null;
        }

        $targetDir = App::config('app', 'uploads_dir') . '/payments';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0775, true);
        }

        $filename = uniqid('payment_', true) . '.' . $ext;
        $target = $targetDir . '/' . $filename;
        if (!move_uploaded_file($file['tmp_name'], $target)) {
            return null;
        }

        return $filename;
    }
}